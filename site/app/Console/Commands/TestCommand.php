<?php

namespace App\Console\Commands;

use App\Models\Offer;
use App\Models\Photo;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
        $sheet->setCellValue('A1', 'Код');
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

        /** @var Product $product */
        foreach (Product::active()->doesntHave('photos')->get() as $i => $product) {
            $sheet->setCellValue('A' . ($i + 2), $product->code);
            $sheet->setCellValue('B' . ($i + 2), $product->barcode ?? '');
            $sheet->setCellValue('C' . ($i + 2), $product->name);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save('Товары без фото.xlsx');

        $this->info(PHP_EOL . 'Процесс завершена!');
        return 0;
    }
}
