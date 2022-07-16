<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

ini_set('memory_limit', -1);

class TestCommand extends Command
{
    protected $signature = 'test {order}';
    protected $description = 'test';

    public function handle(): int
    {
        $orderId = $this->argument('order');
        $code = Artisan::call('send:order', ['order' => $orderId]);
        $message = $code ? 'Произошла ошибка при обработке запроса' : 'Запрос обработан успешно';

        $this->info('Процесс завершена! ' . $message);
        return 0;
    }
}
