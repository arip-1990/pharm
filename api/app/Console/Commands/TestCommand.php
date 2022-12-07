<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

ini_set('memory_limit', -1);

class TestCommand extends Command
{
    protected $signature = 'test';
    protected $description = 'test';

    public function handle(): int
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Товары');
        $sheet->setCellValue('A1', 'Код');
        $sheet->setCellValue('B1', 'Наименование');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);
        $sheet->getStyle('B1')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        $i = 2;
        Product::has('offers')->where(function ($query) {
            $query->whereNull('description')->orWhereHas('values', null, '<=', 5);
        })->chunk(1000, function ($products) use ($sheet, &$i) {
            /** @var Product $product */
            foreach ($products as $product) {
                $sheet->setCellValue('A' . $i, $product->code);
                $sheet->setCellValue('B' . $i, $product->name);

                $i++;
            }
        });

        $writer = new Xlsx($spreadsheet);
        $writer->save(Storage::path("Товары без описания.xlsx"));

        $this->info("Процесс завершена!");
        return 0;
    }
}
