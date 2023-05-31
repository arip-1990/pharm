<?php

namespace App\Console\Commands\Search;

use Carbon\Carbon;
use Elasticsearch\Client;
use Elasticsearch\Common\Exceptions\Missing404Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class InitCommand extends Command
{
    protected $signature = 'search:init';
    protected $description = 'Initialization search index';

    public function __construct(private readonly Client $client)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $startTime = Carbon::now();
        $redis = Redis::connection('bot')->client();
        try {
            $this->initProducts();

            $redis->publish('bot:search', json_encode([
                'success' => true,
                'type' => 'init',
                'message' => 'Индекс товаров проиницилизировано: ' . $startTime->diff(Carbon::now())->format('%iм %sс')
            ], JSON_UNESCAPED_UNICODE));

            return self::SUCCESS;
        }
        catch (\Exception $e) {
            $redis->publish('bot:search', json_encode([
                'success' => false,
                'type' => 'init',
                'message' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE));

            return self::FAILURE;
        }
        finally {
            $startTime = null;
        }
    }

    private function initProducts(): void
    {
        try {
            $this->client->indices()->delete(['index' => 'products']);
        }
        catch (Missing404Exception $e) {}

        $this->client->indices()->create([
            'index' => 'products',
            'body' => [
                'mappings' => [
                    'properties' => [
                        'id' => ['type' => 'keyword'],
                        'name' => ['type' => 'text'],
                        'slug' => ['type' => 'keyword'],
                        'code' => ['type' => 'integer'],
                        'description' => ['type' => 'text'],
                        'categories' => ['type' => 'integer'],
                        'values' => ['type' => 'text'],
                        'cities' => ['type' => 'keyword', 'normalizer' => 'lowercase'],
                    ],
                ],
                'settings' => [
                    'analysis' => [
                        'char_filter' => [
                            'replace' => [
                                'type' => 'mapping',
                                'mappings' => ['&=> and '],
                            ],
                        ],
                        'filter' => [
                            'ru_stop' => [
                                'type' => 'stop',
                                'stopwords' => '_russian_',
                            ],
                            'ru_stemmer' => [
                                'type' => 'stemmer',
                                'language' => 'russian',
                            ],
                            'word_delimiter' => [
                                'type' => 'word_delimiter',
                                'split_on_numerics' => false,
                                'split_on_case_change' => true,
                                'generate_word_parts' => true,
                                'generate_number_parts' => true,
                                'catenate_all' => true,
                                'preserve_original' => true,
                                'catenate_numbers' => true,
                            ],
                        ],
                        'analyzer' => [
                            'default' => [
                                'type' => 'custom',
                                'char_filter' => ['html_strip', 'replace'],
                                'tokenizer' => 'whitespace',
                                'filter' => [
                                    'lowercase',
                                    'word_delimiter',
                                    'ru_stop',
                                    'ru_stemmer',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }
}
