<?php

namespace App\Console\Commands\Import;

use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class EmptyProductCommand extends \Illuminate\Console\Command
{
    protected $signature = 'import:emptyProduct';
    protected $description = 'Import parse products';

    public function handle(): int
    {
        $startTime = Carbon::now();

        try {
            $reader = IOFactory::createReader('Xlsx');
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load(Storage::path('Найденные товары.xlsx'));

            foreach ($spreadsheet->getActiveSheet()->toArray() as $row) {
                /** @var Product $product */
                $product = Product::query()->where('code', (int)$row[0])
                    ->where('status', Product::STATUS_DRAFT)->first();
                if ($product) {
                    if ($photo = explode('|', (string)$row[3])[0]) {
                        try {
                            $info = pathinfo($photo);
                            $info['extension'] = explode('?', $info['extension'])[0];
                            $photo = file_get_contents($photo);
                            do {
                                $fileName = Str::random() . '.' . $info['extension'];
                            }
                            while (Storage::exists('images/original/' . $fileName));

                            Storage::put('images/original/' . $fileName, $photo);

                            $product->photos()->create([
                                'file' => $fileName,
                                'sort' => $product->photos()->count()
                            ]);
                        }
                        catch (\Exception $e) {}
                    }

                    $product->update([
                        'status' => Product::STATUS_MODERATION,
                        'description' => (string)$row[2],
                    ]);
                }
            }
        }
        catch (\RuntimeException $e) {
            $this->error($e->getMessage());
            return 1;
        }

        $this->info('Загрузка успешно завершена! ' . $startTime->diff(Carbon::now())->format('%iм %sс'));
        return 0;
    }
}
