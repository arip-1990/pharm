<?php

namespace App\Console\Commands\Export;

use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class EmptyProductCommand extends Command
{
    protected $signature = 'export:emptyProduct {type=photo}';
    protected $description = 'Export products with
                                {photo (default): has photos}
                                {no-photo: empty photos}
                                {description: empty description}';

    public function handle(): int
    {
        $startTime = Carbon::now();

        try {
            if ($this->argument('type') === 'description')
                $this->description();
            elseif ($this->argument('type') === 'no-photo')
                $this->photos();
            else
                $this->photos(true);
        }
        catch (\Exception $e) {
            $this->error($e->getMessage());
            return 1;
        }

        $this->info('Выгрузка успешно завершена! ' . $startTime->diff(Carbon::now())->format('%mм %sс'));
        return 0;
    }

    private function description(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Товары');
        $sheet->setCellValue('A1', 'Код');
        $sheet->setCellValue('B1', 'Наименование');
        $sheet->setCellValue('C1', 'Ссылка');
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

        $i = 2;
        Product::whereHas('offers', fn(Builder $query) => $query->whereIn('store_id', [
            '6179a810-3e07-11eb-80ec-ac1f6bd1d36d',
            'fcd58bdb-f170-11e9-969d-005056011715',
            'af98853a-f0da-11e9-969d-005056011715',
            'dba0cfa1-ee6e-11e9-969d-005056011715',
            'a9ccff41-f0be-11e9-969d-005056011715',
            'f6b96c60-da8a-11ec-80f4-ac1f6bd1d36d',
            'f36356e9-eaa1-11e9-969d-005056011715',
            '2012fa57-d2a5-11ec-80f4-ac1f6bd1d36d',
            'f1bd373d-da63-11ec-80f4-ac1f6bd1d36d',
            'f4ecaaea-b427-11ec-80f3-ac1f6bd1d36d',
            '6c463751-da64-11ec-80f4-ac1f6bd1d36d'
        ]))->has('values', '<', 5)->chunk(1000, function ($products) use ($sheet, &$i) {
            /** @var Product $product */
            foreach ($products as $product) {
                if (!$product->getValue(1) or !$product->getValue(3) or !$product->getValue(30)) {
                    $sheet->setCellValue('A' . $i, $product->code);
                    $sheet->setCellValue('B' . $i, $product->name);
                    $sheet->setCellValue('C' . $i, route('catalog.product', ['product' => $product]));

                    $i++;
                }
            }
        });

        $date = Carbon::now();
        $writer = new Xlsx($spreadsheet);
        $writer->save(Storage::path("Товары без описания {$date->format('d-m-Y')}.xlsx"));
    }

    private function photos($hasPhotos = false): void
    {
        $date = Carbon::now();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Products');
        $sheet->setCellValue('A1', 'Код');
        $sheet->setCellValue('B1', 'Наименование');
        $sheet->setCellValue('C1', 'Ссылка');
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

        $query = Product::query()->has('offers');
        if ($hasPhotos) $query->has('photos');
        else $query->doesntHave('photos');

        /** @var Product $product */
        foreach ($query->get() as $i => $product) {
            $sheet->setCellValue('A' . ($i + 2), $product->code);
            $sheet->setCellValue('B' . ($i + 2), $product->name);
            $sheet->setCellValue('C' . ($i + 2), route('catalog.product', ['product' => $product]));
        }

        if ($hasPhotos) $fileName = 'Товары с фото ' . $date->format('d-m-Y') . '.xlsx';
        else $fileName = 'Товары без фото ' . $date->format('d-m-Y') . '.xlsx';

        $writer = new Xlsx($spreadsheet);
        $writer->save(Storage::path($fileName));
    }
}
