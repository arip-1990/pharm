<?php

namespace App\Console\Commands;

use App\Models\Photo;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

ini_set('memory_limit', -1);

class TestCommand extends Command
{
    protected $signature = 'test';
    protected $description = 'test';

    public function handle(): int
    {
        $directories = Storage::directories('images/original');
        print_r($directories);
//        $total = count($directories);
//        foreach ($directories as $i => $directory) {
//            echo "\033[2KProcessing: {$i}/{$total}\r";
//            $productId = explode('/', $directory)[2];
//            if (Product::withTrashed()->where('id', $productId)->exists()) {
//                $sort = 0;
//                foreach (Storage::allFiles($directory) as $file) {
//                    $exp = explode('/', $file)[3];
//                    $exp = explode('.', $exp)[1];
//                    do {
//                        $fileName = Str::random() . '.' . $exp;
//                    }
//                    while (Storage::exists('images/original/' . $fileName));
//
//                    if (Storage::move($file, 'images/original/' . $fileName)) {
//                        Photo::query()->create(['product_id' => $productId, 'file' => $fileName, 'sort' => $sort]);
//                        $sort++;
//                    }
//                }
//
//                Storage::deleteDirectory($directory);
//            }
//        }
        echo PHP_EOL;
        $this->info(PHP_EOL . 'Очистка успешно завершена!');
        return 0;
    }
}
