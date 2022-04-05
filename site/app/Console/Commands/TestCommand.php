<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

ini_set('memory_limit', -1);

class TestCommand extends Command
{
    protected $signature = 'test {file}';
    protected $description = 'test';

    public function handle(): int
    {
       $total = Product::withTrashed()->count();
       $i = 1;
       Product::withTrashed()->chunk(1000, function ($products) use ($total, &$id, &$i) {
           /** @var Product $product */
           foreach ($products as $product) {
               $photoCount = $product->photos->count();

               echo "\033[2KProcessing: " . $i . '/' . $total . "\t({$photoCount})" . "\r";

               if ($photoCount > 5) {
                   for ($j = 0; $j < $photoCount - 5; $j++) {
                       $photo = $product->photos->get($j);
                       $files = glob(Storage::path("images/original/{$photo->product_id}") . "/{$photo->id}.*");
                       if (isset($files[0])) {
                           if (unlink($files[0])) {
                               $photo->delete();
                           }
                       }
                       else {
                           $photo->delete();
                       }
                   }
               }

               $i++;
           }
       });

        $this->info(PHP_EOL . 'Чистка успешно завершена!');
        return 0;
    }
}
