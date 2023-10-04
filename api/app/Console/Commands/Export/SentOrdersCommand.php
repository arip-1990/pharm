<?php

namespace App\Console\Commands\Export;

use App\Order\Entity\Order;
use App\Order\Entity\Payment;
use App\Order\Entity\Status\OrderStatus;
use App\Order\Entity\Status\Status;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class SentOrdersCommand extends Command
{
    protected $signature = 'export:sentOrders';
    protected $description = 'Export sent orders to 1C';

    public function handle(): int
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Заказы');
        $sheet->setCellValue('A1', 'Номер');
        $sheet->setCellValue('B1', 'Номер 1C');
        $sheet->setCellValue('C1', 'Дата создания');
        $sheet->setCellValue('D1', 'Имя клиента');
        $sheet->setCellValue('E1', 'Телефон клиента');
        $sheet->setCellValue('F1', 'Аптека');
        $sheet->setCellValue('G1', 'Оплата');
        $sheet->setCellValue('H1', 'Сумма');
        $sheet->setCellValue('I1', 'Платформа');
        $sheet->setCellValue('J1', 'Статус');

        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'wrapText' => true]
        ]);
        $sheet->getStyle('B1')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'wrapText' => true]
        ]);
        $sheet->getStyle('C1')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'wrapText' => true]
        ]);
        $sheet->getStyle('D1')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'wrapText' => true]
        ]);
        $sheet->getStyle('E1')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'wrapText' => true]
        ]);
        $sheet->getStyle('F1')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'wrapText' => true]
        ]);
        $sheet->getStyle('G1')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'wrapText' => true]
        ]);
        $sheet->getStyle('H1')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'wrapText' => true]
        ]);
        $sheet->getStyle('I1')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'wrapText' => true]
        ]);
        $sheet->getStyle('J1')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'wrapText' => true]
        ]);

        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setWidth(36);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setWidth(64);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        $sheet->getColumnDimension('H')->setAutoSize(true);
        $sheet->getColumnDimension('I')->setAutoSize(true);
        $sheet->getColumnDimension('J')->setAutoSize(true);

        $year = Carbon::now()->startOfYear();
        $groups = [];
        $i = 2;
        Order::whereBetween('created_at', [$year, $year->clone()->endOfYear()])->orderBy('created_at')->each(function (Order $order) use ($sheet, &$groups, &$i) {
            if (!$order->isSent() and !$order->inStatus(OrderStatus::STATUS_PROCESSING)) return;

            $cost = $order->cost;
            if ($group = $order->group) {
                if (in_array($group->id, $groups)) return;

                $cost = $group->orders->sum('cost');
                $groups[] = $group->id;
            }

            /** @var Status $status */
            $sheet->setCellValue('A' . $i, $order->id);
            $sheet->setCellValue('B' . $i, $order->group?->order_1c_id ?? $order->id);
            $sheet->setCellValue('C' . $i, $order->created_at->format('d-m-Y H:i'));
            $sheet->setCellValue('D' . $i, $order->name);
            $sheet->setCellValue('E' . $i, $order->phone);
            $sheet->setCellValue('F' . $i, $order->store->name);
            $sheet->setCellValue('G' . $i, $order->payment?->isType(Payment::TYPE_CARD) ? 'Картой' : 'Наличными');
            $sheet->setCellValue('H' . $i, $cost . '₽');
            $sheet->setCellValue('I' . $i, $order->platform);
            $sheet->setCellValue('J' . $i, Order::getStatusLabel($order->statuses->last()));

            $i++;
        });

        $date = Carbon::now();
        $writer = new Xlsx($spreadsheet);
        $writer->save(Storage::path("Список заказов {$date->format('d-m-Y')}.xlsx"));

        return self::SUCCESS;
    }
}
