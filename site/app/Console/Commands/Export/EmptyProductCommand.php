<?php

namespace App\Console\Commands\Export;

use App\Models\Offer;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class EmptyProductCommand extends Command
{
    protected $signature = 'export:emptyProduct {type=photo}';
    protected $description = 'Export products with
                                {photo (default) : empty photos}
                                {attribute : empty attributes}
                                {description : empty description}';

    public function handle(): int
    {
        $startTime = Carbon::now();

        try {
            switch ($this->argument('type')) {
                case 'attribute':
                    $this->attributes();
                    break;
                case 'description':
                    $this->description();
                    break;
                default:
                    $this->photos();
            }
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
        $sheet->setTitle('Products');
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

        $productIds = Offer::query()->select('product_id')->groupBy('product_id')->get()->pluck('product_id');
        /** @var Product $product */
        foreach (Product::query()->whereIn('id', $productIds)->whereNull('description')->get() as $i => $product) {
            $sheet->setCellValue('A' . ($i + 2), $product->code);
            $sheet->setCellValue('B' . ($i + 2), $product->name);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save(Storage::path('Товары без описания.xlsx'));
    }

    private function attributes(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Products');
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

        $productIds = Offer::query()->select('product_id')->groupBy('product_id')->get()->pluck('product_id');
        /** @var Product $product */
        foreach (Product::query()->whereIn('id', $productIds)->doesntHave('values')->get() as $i => $product) {
            $sheet->setCellValue('A' . ($i + 2), $product->code);
            $sheet->setCellValue('B' . ($i + 2), $product->name);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save(Storage::path('Товары без аттрибутов.xlsx'));
    }

    private function photos(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Products');
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

        $productIds = Offer::query()->select('product_id')->groupBy('product_id')->get()->pluck('product_id');
        /** @var Product $product */
        foreach (Product::query()->whereIn('id', $productIds)->doesntHave('photos')->get() as $i => $product) {
            $sheet->setCellValue('A' . ($i + 2), $product->code);
            $sheet->setCellValue('B' . ($i + 2), $product->name);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save(Storage::path('Товары без фото.xlsx'));
    }
}
