<?php

namespace App\Http\Controllers\V1\Panel\Product;

use App\Http\Resources\ProductResource;
use App\Product\Entity\Product;
use App\Product\UseCase\SearchService;
use Illuminate\Database\Query\Expression;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Routing\Controller;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class IndexController extends Controller
{
    public function handle(Request $request, SearchService $searchService): ResourceCollection
    {
        $query = Product::select('products.*');

        if ($request->get('searchColumn')) {
            if ($request->get('searchColumn') === 'name') {
                $data = $searchService->search(
                    $request->get('searchText'),
                    $request->get('page', 1) - 1,
                    $request->get('pageSize', 10)
                );
                $ids = array_column($data, 'id');

                $query->whereIn('id', $ids)->orderBy(new Expression("position(id::text in '" . implode(',', $ids) . "')"));
            }
            else {
                $query->where($request->get('searchColumn'), '~*', $request->get('searchText'));
            }
        }

        if ($status = $request->get('offer')) {
            $status === 'on' ? $query->has('offers') : $query->doesntHave('offers');
        }

        if ($sale = $request->get('sale')) {
            $query->where('sale', $sale === 'on');
        }

        if ($photo = $request->get('photo')) {
            switch ($photo) {
                case 'present':
                    $query->has('photos');
                    break;
                case 'missing':
                    $query->doesntHave('photos');
            }
        }

        if ($category = $request->get('category')) {
            $category === 'on' ? $query->whereNotNull('category_id') : $query->whereNull('category_id');
        }

        if ($request->get('orderField')) {
            if ($request->get('orderField') === 'category') {
                $query->join('categories', 'categories.id', '=', 'products.category_id')
                    ->orderBy('categories.name', $request->get('orderDirection'));
            }
            elseif ($request->get('orderField') === 'barcode') {
                $query->orderByRaw('json_array_length(barcodes) ' . $request->get('orderDirection'));
            }
            else {
                $query->orderBy($request->get('orderField'), $request->get('orderDirection'));
            }
        }

        return ProductResource::collection($query->paginate($request->get('pageSize', 10)));
    }


    public function exportWithoutPhotos(Request $request): StreamedResponse
    {
        // Номер страницы (по умолчанию 1)
        $page = (int) $request->get('page', 1);
        $perPage = 1000;
        $offset = ($page - 1) * $perPage;

        // Загружаем продукты без фото с пагинацией
        $products = Product::doesntHave('photos')
            ->skip($offset)
            ->take($perPage)
            ->get();

        // Создаём Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Заголовки
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Название');
        $sheet->setCellValue('C1', 'Slug');
        $sheet->setCellValue('D1', 'Код 1c');
        $sheet->setCellValue('E1', 'Описание');
        $sheet->setCellValue('F1', 'Дата создания');

        $row = 2;

        foreach ($products as $product) {
            $sheet->setCellValue("A{$row}", $product->id);
            $sheet->setCellValue("B{$row}", $product->name);
            $sheet->setCellValue("C{$row}", $product->slug);
            $sheet->setCellValue("D{$row}", $product->code);
            $sheet->setCellValue("E{$row}", $product->description ?? '');
            $sheet->setCellValue("F{$row}", $product->created_at);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);

        // Отдаём файл на скачивание
        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="products_without_photos_page_' . $page . '.xlsx"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }
}
