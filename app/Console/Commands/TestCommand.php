<?php

namespace App\Console\Commands;

use App\Entities\Category;
use App\Entities\Photo;
use App\Entities\Product;
use App\Entities\Value;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\FileNotFoundException;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as Reader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

ini_set('memory_limit', -1);

class TestCommand extends Command
{
    protected $signature = 'test {file}';
    protected $description = 'test';

    protected function categoryDepth(Collection $categories): int
    {
        $max = 0;
        foreach ($categories as $category) {
            $depth = $this->categoryDepth($category->children);
            if ($depth > $max)
                $max = $depth;
        }

        return $max + 1;
    }

    public function handle(): int
    {
//       $this->storeProducts($this->argument('file'));
         $this->export();

//        $total = Product::query()->count();
//        $i = 1;
//        Product::query()->chunk(1000, function ($products) use ($total, &$i) {
//            /** @var Product $product */
//            foreach ($products as $product) {
//                echo "\033[2KProcessing: " . $i . '/' . $total . "\r";
//
//                $found = false;
//                foreach ($product->photos as $photo) {
//                    if (Storage::exists($photo->getOriginalFile())) {
//                        $exp = explode('.', $photo->file);
//                        $fileName = $product->id . '_' . $photo->id . '.' . strtolower($exp[count($exp) - 1]);
//                        if ($photo->file !== $fileName) {
//                            Storage::move($photo->getOriginalFile(), '/images/product/original/' . $fileName);
//                            $photo->update(['file' => $fileName]);
//                        }
//                    }
//                    else $photo->delete();
//                }
//
//                $product->update();

//                if (!$found) {
//                    $photos = [];
//                    $id = Photo::getNextId();
//
//                    if ($files = glob("storage/app/images/product/original/$product->id*")) {
//                        foreach ($files as $file) {
//                            $exp = explode('/', $file);
//                            $file = $exp[count($exp) - 1];
//                            $exp = explode('.', $file);
//                            $exp = $exp[count($exp) - 1];
//                            $fileName = $product->id . '_' . $id . '.' . $exp;
//                            /** @var Photo $photo */
//                            $photo = new Photo(['type' => Photo::TYPE_PICTURE, 'file' => $fileName]);
//                            Storage::move("/images/product/original/$file", $photo->getOriginalFile());
//                            $photos[] = $photo;
//                            $id++;
//                        }
//                    }
//                    elseif ($files = glob("products/$product->id*")) {
//                        foreach ($files as $file) {
//                            $exp = explode('/', $file);
//                            $file = $exp[count($exp) - 1];
//                            $exp = explode('.', $file);
//                            $exp = $exp[count($exp) - 1];
//                            $fileName = $product->id . '_' . $id . '.' . $exp;
//                            /** @var Photo $photo */
//                            $photo = new Photo(['type' => Photo::TYPE_PICTURE, 'file' => $fileName]);
//                            rename("products/$file", 'storage/app' . $photo->getOriginalFile());
//                            $photos[] = $photo;
//                            $id++;
//                        }
//                    }
//
//                    if ($photos) {
//                        $product->photos()->saveMany($photos);
//                        $product->update();
//                    }
//                }

//                $i++;
//            }
//        });

        $this->info(PHP_EOL . 'Загрузка успешно завершена!');
        return 0;
    }

    public function export(): void
    {
        $data = [];
        Product::query()->where('status', true)->chunk(1000, function (Collection $products) use (&$data) {
            /** @var Product $product */
           foreach ($products as $product) {
               if ($product->photos()->count()) {
                   $found = false;
                   foreach ($product->photos as $photo) {
                       if (!Storage::exists($photo->getOriginalFile()))
                           $found = true;
                   }

                  if ($found) {
                      $data[] = [
                          'code' => $product->code,
                          'barcode' => $product->barcode,
                          'vendor' => $product->getValue(1),
                          'name' => $product->name
                      ];
                  }
               }
           }
        });

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Код товара');
        $sheet->setCellValue('B1', 'Штрих-код');
        $sheet->setCellValue('C1', 'Производитель');
        $sheet->setCellValue('D1', 'Наименование');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);
        $sheet->getStyle('B1')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);
        $sheet->getStyle('C1')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);
        $sheet->getStyle('D1')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        foreach ($data as $i => $item) {
            $sheet->setCellValue('A' . ($i + 2), $item['code']);
            $sheet->setCellValue('B' . ($i + 2), $item['barcode']);
            $sheet->setCellValue('C' . ($i + 2), $item['vendor']);
            $sheet->setCellValue('D' . ($i + 2), $item['name']);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->setPreCalculateFormulas(false);
        $writer->save('Товары без фото.xlsx');
    }

    private function compareBarcode(string $file): void
    {
        $reader = new Reader();
        $sheet = $reader->load($file);
        $sheet = $sheet->getActiveSheet();

        $data = [];
        Product::query()->chunk(1000, function (Collection $products, int $page) use ($sheet, &$data) {
            $loader = ['|', '/', '-', '\\'];
            $notFound = 0;
            $i = 1;
            $data = [];
            /** @var Product $product */
            foreach ($products as $product) {
                $found = false;
                echo "\033[2K " . $loader[$i % 4] . "  Завершено: $i/1000\r";

                foreach ($sheet->getRowIterator() as $row) {
                    $cells = $row->getCellIterator();
                    $barcode = $cells->current()->getValue();
                    if (!is_numeric($barcode) or strlen($barcode) !== 13) continue;

                    $cells->next();
                    $cells->next();
                    $code =  str_replace(',', '', $cells->current()->getValue());
                    if ($code == $product->code) {
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    $data[] = [
                        'code' => $product->code,
                        'barcode' => $product->barcode,
                        'name' => $product->name
                    ];
                    $notFound++;
                }
                $i++;
            }

            echo "\033[2KЗавершено: $i/1000. Не найдено товаров: $notFound\n";
            echo "Запись в excel...\r";

            $this->exportCompareProducts($data, 'Список товаров без штрих-кода_' . $page);

            echo "Запись в excel... Ok\n";
        });

        foreach ($sheet->getRowIterator() as $row) {
            $cells = $row->getCellIterator();
            $barcode = $cells->current()->getValue();
            if ($barcode === 'Штрихкод') continue;

            $cells->next();
            $name = $cells->current()->getValue();
            $cells->next();
            $code =  str_replace(',', '', $cells->current()->getValue());

            if (!is_numeric($barcode) or strlen($barcode) !== 13) {
                $data[] = [
                    'code' => $code,
                    'barcode' => $barcode,
                    'name' => $name
                ];
            }
        }

        echo "Запись товаров с неправильными штрих-кодами в excel...\r";

        $this->exportCompareProducts($data, 'Список товаров с неправильными штрих-кодами_');

        echo "Запись в excel... Ok\n";
    }

    private function exportCompareProducts(array $data, string $fileName): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Код товара');
        $sheet->setCellValue('B1', 'Штрих-код');
        $sheet->setCellValue('C1', 'Наименование');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);
        $sheet->getStyle('B1')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);
        $sheet->getStyle('C1')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        foreach ($data as $i => $item) {
            $sheet->setCellValue('A' . ($i + 2), $item['code']);
            $sheet->setCellValue('B' . ($i + 2), $item['barcode']);
            $sheet->setCellValue('C' . ($i + 2), $item['name']);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->setPreCalculateFormulas(false);
        $writer->save($fileName . '.xlsx');
    }

    private function storeProducts(string $file): void
    {
        $reader = new Reader();
        $sheet = $reader->load($file)->getActiveSheet();
        $cnt = count($sheet->toArray());

        $errors = [];
        foreach ($sheet->toArray() as $i => $row) {
            echo "\033[2KЗавершено: " . ($i + 1) . '/' . $cnt . "\r";
            /** @var Product $product */
            if (!$product = Product::query()->where('code', trim($row[0]))->first()) {
                $errors[] = trim($row[0]) . PHP_EOL;
                continue;
            }

            echo "\033[2KЗавершено: \033[1;32m" . ($i + 1) . '/' . $cnt . "\033[0m\r";

            if (trim($row[9]))
                $product->description = trim($row[9]);

            if (trim($row[3])) {
                $product->values()->where('attribute_id', 1)->delete();
                $product->values()->create(['attribute_id' => 1, 'value' => trim($row[3])]);
            }

            if (trim($row[4])) {
                $product->values()->where('attribute_id', 2)->delete();
                $product->values()->create(['attribute_id' => 2, 'value' => trim($row[4])]);
            }

            if (trim($row[5])) {
                $product->values()->where('attribute_id', 62)->delete();
                $product->values()->create(['attribute_id' => 62, 'value' => trim($row[5])]);
            }

            if (trim($row[6])) {
                $product->values()->where('attribute_id', 3)->delete();
                $product->values()->create(['attribute_id' => 3, 'value' => trim($row[6])]);
            }

            if (trim($row[7])) {
                $product->values()->where('attribute_id', 39)->delete();
                $product->values()->create(['attribute_id' => 39, 'value' => trim($row[7])]);
            }

            if (trim($row[8])) {
                $product->values()->where('attribute_id', 30)->delete();
                $product->values()->create(['attribute_id' => 30, 'value' => trim($row[8])]);
            }

//            foreach (explode('|', $row[3]) as $item) {
//                $tmp = explode('#', $item);
//                if (2 > count($tmp) or !trim($tmp[0]) or !trim($tmp[1]))
//                    continue;
//
//                switch (trim($tmp[0])) {
//                    case 'Производитель':
//                    case 'vendor':
//                        $product->values()->where('attribute_id', 1)->delete();
//                        $product->values()->create(['attribute_id' => 1, 'value' => trim($tmp[1])]);
//                        break;
//                    case 'country':
//                        $product->values()->where('attribute_id', 2)->delete();
//                        $product->values()->create(['attribute_id' => 2, 'value' => trim($tmp[1])]);
//                        break;
//                    case 'brand':
//                        $product->values()->where('attribute_id', 62)->delete();
//                        $product->values()->create(['attribute_id' => 62, 'value' => trim($tmp[1])]);
//                        break;
//                    case 'Состав':
//                    case 'composition':
//                        $product->values()->where('attribute_id', 30)->delete();
//                        $product->values()->create(['attribute_id' => 30, 'value' => trim($tmp[1])]);
//                        break;
//                    case 'Описание':
//                    case 'description':
//                        $product->description = trim($tmp[1]);
//                        break;
//                    case 'Действующее вещество':
//                    case 'substance':
//                        $product->values()->where('attribute_id', 3)->delete();
//                        $product->values()->create(['attribute_id' => 3, 'value' => trim($tmp[1])]);
//                        break;
//                    case 'Лекарственная форма':
//                        $product->values()->where('attribute_id', 5)->delete();
//                        $product->values()->create(['attribute_id' => 5, 'value' => trim($tmp[1])]);
//                        break;
//                    case 'Условия хранения':
//                        $product->values()->where('attribute_id', 39)->delete();
//                        $product->values()->create(['attribute_id' => 39, 'value' => trim($tmp[1])]);
//                }
//            }

           if ($photos = $this->downloadImage($product->id, explode('|', $row[10]))) {
               echo "\033[2KЗавершено: \033[1;32m" . ($i + 1) . '/' . $cnt . "\t" . count($photos) . "\033[0m\r";
               Storage::delete($product->photos->map(fn (Photo $photo) => $photo->getOriginalFile()));
               $product->photos()->delete();
               $product->photos()->saveMany($photos);
           }

           $product->update();
        }

        file_put_contents('codes.txt', $errors);
    }

    /** @return Photo[] */
    private function downloadImage(string $productId, array $urls): array
    {
        $photos = [];
        $id = Photo::getNextId();
        try {
            foreach ($urls as $url) {
                $url = trim($url);
                if (!$url or false !== stripos($url, 'no-photo') or false !== stripos($url, 'no_photo'))
                    continue;

                $exp = explode('.', explode('?', $url)[0]);
                $exp = $exp[count($exp) - 1];
                $fileName = $productId . '_' . $id . '.' . $exp;
                $file = file_get_contents($url);
                /** @var Photo $photo */
                $photo = new Photo(['type' => Photo::TYPE_PICTURE, 'file' => $fileName]);
                Storage::put($photo->getOriginalFile(), $file);
                $photos[] = $photo;
                $id++;
            }
        }
        catch (\Exception $exception) {
            echo PHP_EOL . $exception->getMessage() . PHP_EOL;
        }

        return $photos;
    }
}
