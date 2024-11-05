<?php

namespace App\Http\Controllers\V1\Panel\Order;

use App\Http\Resources\OrderResource;
use App\Order\Entity\Order;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller;

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;


class IndexController extends Controller
{
    public function handle(Request $request): ResourceCollection | JsonResponse
    {
        try {
            $query = Order::select('orders.*');
            if ($platform = $request->get('platform')) {
                if ($platform == 'mobile') $query->whereIn('platform', ['android', 'ios']);
                else $query->where('platform', $platform);
            }

            if ($user = $request->get('userName')) $query->where('user_id', $user);

            if ($field = $request->get('orderField')) {
                switch ($field) {
                    case 'userName':
                        $query->join('users', 'users.id', 'orders.user_id')
                            ->orderBy('users.name', $request->get('orderDirection'));
                        break;
                    case 'userPhone':
                        $query->join('users', 'users.id', 'orders.user_id')
                            ->orderBy('users.phone', $request->get('orderDirection'));
                        break;
                    case 'store':
                        $query->join('stores', 'stores.id', 'orders.store_id')
                            ->orderBy('stores.name', $request->get('orderDirection'));
                        break;
                    default:
                        $query->orderBy($field, $request->get('orderDirection'));
                }
            }
            else $query->orderByDesc('created_at');

            $page = $request->get('page', 1);
            $pageSize = $request->get('pageSize', 10);
            $total = $query->count();
            $missed = [];
            $orders = $query->offset(($page - 1) * $pageSize)->take($pageSize)->get()->filter(function (Order $item) use (&$missed) {
                if ($item->order_group_id and $item->delivery_id == 3 and !in_array($item->order_group_id, $missed)) {
                    $missed[] = $item->order_group_id;
                    return false;
                }

                return true;
            });

            return OrderResource::collection(new LengthAwarePaginator($orders, $total, $pageSize));
        }
        catch (\Exception $exception) {
            return new JsonResponse([
                'code' => $exception->getCode(),
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    public function sendOrder(Request $request)
    {
        $query = Order::select('orders.*');

        if ($platform = $request->get('platform')) {
            if ($platform == 'mobile') $query->whereIn('platform', ['android', 'ios']);
            else if ($platform == 'web') $query->where('platform', 'web');
        }

        if ($request->get('firstDate') != 'undefined')
            $query->whereBetween('created_at',[
                Carbon::parse($request->get('firstDate')),
                Carbon::parse($request->get('lastDate'))
            ]);

        $query->orderByDesc('created_at');

        $page = 1;
        $pageSize = $request->get('size', 10);
        $total = $query->count();


        $missed = [];
        $orders = $query->offset(($page - 1) * $pageSize)->take($pageSize)->get()->filter(function (Order $item) use (&$missed) {
            if ($item->order_group_id and $item->delivery_id == 3 and !in_array($item->order_group_id, $missed)) {
                $missed[] = $item->order_group_id;
                return false;
            }

            return true;
        });

            return OrderResource::collection(new LengthAwarePaginator($orders, $total, $pageSize))->collection;

    }

    public function exportOrder(Request $request)
    {
        try {

            $spreadsheet = new Spreadsheet();
            $activeWorksheet = $spreadsheet->getActiveSheet();
            $activeWorksheet->setCellValue('A1', 'id');
            $activeWorksheet->setCellValue('B1', 'Имя');
            $activeWorksheet->setCellValue('C1', 'Телефон');
            $activeWorksheet->setCellValue('D1', 'Адрес аптеки');
            $activeWorksheet->setCellValue('E1', 'платформа');
            $activeWorksheet->setCellValue('F1', 'Заказ принят');
            $activeWorksheet->setCellValue('G1', 'отправка почты');
            $activeWorksheet->setCellValue('H1', 'отправка в 1с');
            $activeWorksheet->setCellValue('I1', 'заказ собран');
            $activeWorksheet->setCellValue('J1', 'заказ получен');
            $activeWorksheet->setCellValue('K1', 'Дата');
            $activeWorksheet->setCellValue('L1', 'Сумма заказа');

            $data = $this->sendOrder($request);

            $line = 2;
            foreach ($data as $da){
                $id = $da->resource->id;
                $name = $da->resource->name;
                $phone = $da->resource->phone;
                $storeName = $da->resource->store->name;
                $platform = $da->resource->platform;
                $date = Carbon::parse($da->resource->created_at);
                $cost = $da->resource->cost;

                $statuses = $da->resource->statuses;
                $statusValues = [
                    'M' => '-', // Заказ принят
                    'S' => '-', // Отправка почты
                    'H' => '-', // Отправка в 1с
                    'F' => '-', // Заказ собран
                ];

                foreach ($statuses as $status) {
                    switch ($status->value->value) {
                        case 'M':
                            $statusValues['M'] = ' Отправлено';
                            break;
                        case 'S':
                            $statusValues['S'] = 'отправка в 1с';
                            break;
                        case 'H':
                            $statusValues['H'] = 'Собран';
                            break;
                        case 'F':
                            $statusValues['F'] = 'Получен';
                            break;

                    }
                }

                // Записываем данные в ячейки
                $activeWorksheet->setCellValue("A$line", $id);
                $activeWorksheet->setCellValue("B$line", $name);
                $activeWorksheet->setCellValue("C$line", $phone);
                $activeWorksheet->setCellValue("D$line", $storeName);
                $activeWorksheet->setCellValue("E$line", $platform);
                $activeWorksheet->setCellValue("F$line", "Принят"); // Заказ принят
                $activeWorksheet->setCellValue("G$line", $statusValues['M']); // Отправка почты
                $activeWorksheet->setCellValue("H$line", $statusValues['S']); // Отправка в 1с
                $activeWorksheet->setCellValue("I$line", $statusValues['H']); // Заказ собран
                $activeWorksheet->setCellValue("J$line", $statusValues['F']); // Заказ получен
                $activeWorksheet->setCellValue("K$line", $date); // Дата
                $activeWorksheet->setCellValue("L$line", $cost); // Сумма заказа


                $line++;
            }
            $writer = new Xlsx($spreadsheet);
            $writer->save(storage_path('app/order.xlsx'));
            return response()->download(storage_path('app/order.xlsx'))->deleteFileAfterSend();
        }
        catch (\Exception $exception) {
            return new JsonResponse([
                'code' => $exception->getCode(),
                'message' => $exception->getMessage()
            ], 500);
        }
    }
}
