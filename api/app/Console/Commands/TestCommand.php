<?php

namespace App\Console\Commands;

use App\Models\Offer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Status;
use App\Models\User;
use App\Models\VisitStatistic;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
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
//        try {
//            Order::all()->each(function (Order $order) {
//                $order->statuses->each(function (Status $status) {
//                    $status->state = Status::STATE_SUCCESS;
//                });
//                $order->save();
//            });
//        }
//        catch (\RuntimeException $e) {
//            $this->error($e->getMessage());
//        }

        $this->info('Процесс завершена!');
        return 0;
    }

    private function export(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Товары без описания');
        $sheet->setCellValue('A1', 'Код товара');
        $sheet->setCellValue('B1', 'Наименование');
        $sheet->setCellValue('C1', 'Ссылка на товар');
        $sheet->setCellValue('D1', 'Ссылка на картинку');
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

        $i = 2;
        $client = new Client();
        $productIds = Offer::query()->select('product_id')->groupBy('product_id')->get()->pluck('product_id');
        Product::query()->whereIn('id', $productIds)->doesntHave('photos')->chunk(1000, function ($products) use ($sheet, $client, &$i) {
            $baseUrl = 'http://xn--12080-6ve4g.xn--p1ai/catalog/product?code=';
            /** @var Product $product */
            foreach ($products as $product) {
                $res = $client->get($baseUrl . $product->code);
                if ($res->getStatusCode() != 200)
                    continue;

                $page = (string)$res->getBody();
                preg_match('/<img.+src="(\/images\/origin\/products\/.+?)".+?>/', $page, $matches);
                if (count($matches)) {
                    $sheet->setCellValue('A' . $i, $product->code);
                    $sheet->setCellValue('B' . $i, $product->name);
                    $sheet->setCellValue('C' . $i, $baseUrl . $product->code);
                    $sheet->setCellValue('D' . $i, 'http://xn--12080-6ve4g.xn--p1ai' . $matches[1]);

                    $i++;
                }
            }
        });

        $writer = new Xlsx($spreadsheet);
        $writer->save(Storage::path('Список.xlsx'));
    }

    private function import(): void
    {
        $reader = IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load(Storage::path('Фото.xlsx'));

        foreach ($spreadsheet->getActiveSheet()->toArray() as $row) {
            /** @var Product $product */
            $product = Product::query()->where('code', (int)$row[0])->first();
            if ($product) {
                $exp = explode('/', $row[1]);
                $exp = explode('.', $exp[count($exp) - 1])[1];
                $photo = file_get_contents($row[1]);
                do {
                    $fileName = Str::random() . '.' . $exp;
                }
                while (Storage::exists('images/original/' . $fileName));

                Storage::put('images/original/' . $fileName, $photo);

                $product->photos()->create([
                    'file' => $fileName,
                    'sort' => $product->photos()->count()
                ]);
            }
        }
    }
}
