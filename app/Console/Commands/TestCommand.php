<?php

namespace App\Console\Commands;

use App\Entities\Photo;
use App\Entities\Product;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as Reader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

ini_set('memory_limit', -1);

class TestCommand extends Command
{
    protected $signature = 'test {file}';
    protected $description = 'test';

    public function handle(): int
    {
//       $this->storeProducts($this->argument('file'));
        // $this->export();

        $this->info(PHP_EOL . 'Загрузка успешно завершена!');
        return 0;
    }

    public function export(): void
    {
        $codes = explode(PHP_EOL, file_get_contents('./product_codes.txt'));
        $data = [];
        Product::query()->whereIn('code', $codes)->chunk(1000, function (Collection $products) use (&$data) {
            /** @var Product $product */
           foreach ($products as $product) {
               $data[] = [
                   'code' => $product->code,
                   'barcode' => $product->barcode,
                   'vendor' => $product->getValue(1),
                   'name' => $product->name
               ];
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
        $writer->save('товары без описания.xlsx');
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
            /** @var Product $product */
            if (is_numeric($row[0]) and $product = Product::query()->where('code', trim($row[0]))->first()) {
                echo 'Завершено: ' . ($i + 1) . '/' . $cnt . "\r";

//                if ($description = trim($row[9]))
//                    $product->update(['description' => $description]);

                foreach (explode('|', $row[3]) as $item) {
                    $exp = explode(':', $item);
                    if (2 > count($exp) or !trim($exp[0]) or !trim($exp[1]))
                        continue;

                    switch (trim($exp[0])) {
                        case 'Взаимодействие':
                            if ($tmp = $product->values()->where('attribute_id', 47)->first())
                                $tmp->update(['value' => trim($exp[1])]);
                            else
                                $product->values()->create(['attribute_id' => 47, 'value' => trim($exp[1])]);
                            break;
                        case 'Действующее вещество':
                            if ($tmp = $product->values()->where('attribute_id', 3)->first())
                                $tmp->update(['value' => trim($exp[1])]);
                            else
                                $product->values()->create(['attribute_id' => 3, 'value' => trim($exp[1])]);
                            break;
                        case 'Лекарственная форма':
                            if ($tmp = $product->values()->where('attribute_id', 5)->first())
                                $tmp->update(['value' => trim($exp[1])]);
                            else
                                $product->values()->create(['attribute_id' => 5, 'value' => trim($exp[1])]);
                            break;
                        case 'Назначение':
                            if ($tmp = $product->values()->where('attribute_id', 45)->first())
                                $tmp->update(['value' => trim($exp[1])]);
                            else
                                $product->values()->create(['attribute_id' => 45, 'value' => trim($exp[1])]);
                            break;
                    }
                }

               if ($photos = $this->downloadImage(explode('|', $row[3]))) {
                   Storage::delete($product->photos->map(fn (Photo $photo) => $photo->getOriginalFile()));
                   $product->photos()->delete();
                   $product->photos()->saveMany($photos);
               }
            }
            else
                $errors[] = trim($row[0]) . PHP_EOL;
        }
        file_put_contents('codes.txt', $errors);
    }

    /** @return Photo[] */
    private function downloadImage(array $urls): array
    {
        $photos = [];
        try {
            foreach ($urls as $url) {
                $url = trim($url);
                if (!$url or false !== stripos($url, 'no-photo'))
                    continue;

                $exp = explode('.', explode('?', $url)[0]);
                $exp = $exp[count($exp) - 1];
                $fileName = time() . '.' . $exp;
                $file = file_get_contents($url);
                /** @var Photo $photo */
                $photo = Photo::query()->make(['type' => Photo::TYPE_PICTURE, 'file' => $fileName]);
                Storage::put($photo->getOriginalFile(), $file);
                $photos[] = $photo;
                sleep(1);
            }
        }
        catch (\Exception $exception) {
            echo PHP_EOL . $exception->getMessage() . PHP_EOL;
        }

        return $photos;
    }
}
