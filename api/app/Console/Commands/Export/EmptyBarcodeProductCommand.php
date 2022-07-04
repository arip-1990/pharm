<?php

namespace App\Console\Commands\Export;

use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class EmptyBarcodeProductCommand extends Command
{
    protected $signature = 'export:emptyBarcodeProduct';
    protected $description = 'Export empty barcode with products';

    public function handle(): int
    {
        $startTime = Carbon::now();

        try {
            $this->export();
        }
        catch (\RuntimeException $e) {
            $this->error($e->getMessage());
            return 1;
        }

        $this->info('Загрузка успешно завершена! ' . $startTime->diff(Carbon::now())->format('%mм %sс'));
        return 0;
    }

    private function export(): void
    {
        $data = [];
        Product::query()->chunk(1000, function (Collection $products) use (&$data) {
            foreach ($products as $product) {
                if (!$product->barcode)
                    $data[] = ['code' => $product->code, 'name' => $product->name];
            }
        });

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Код товара');
        $sheet->setCellValue('B1', 'Наименование');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);
        $sheet->getStyle('B1')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        foreach ($data as $i => $item) {
            $sheet->setCellValue('A' . ($i + 2), $item['code']);
            $sheet->setCellValue('B' . ($i + 2), $item['name']);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save('Товары без штрих-кода.xlsx');
    }
}
