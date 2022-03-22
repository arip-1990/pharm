<?php

namespace App\Console\Commands\Image;

use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CheckCommand extends Command
{
    protected $signature = 'image:check';
    protected $description = 'Command description';

    public function handle(): int
    {
        $startTime = Carbon::now();
        $directories = Storage::allDirectories('images/product/original');
        $count = count($directories);
        foreach ($directories as $i => $directory) {
            echo 'Перемещение файлов: ' . ($i + 1) . '/' . $count . "\r";

            $productId = explode('/', $directory)[3];
            /** @var Product $product */
            $product = Product::query()->find($productId);
            foreach (Storage::files($directory) as $file) {
                $fileName = time() . '.' . explode('.', $file)[1];
                $product->addPhoto($fileName);
                rename('/home/arip/Projects/php/pharm/storage/app/' . $file, Storage::path('images/product/original/') . $fileName);
                sleep(1);
            }
            $product->update();
            rmdir('/home/arip/Projects/php/pharm/storage/app/' . $directory);
        }

        $this->info('Загрузка успешно завершена! ' . $startTime->diff()->format('%mм %sс'));
        return 0;
    }
}
