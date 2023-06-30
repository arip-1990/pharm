<?php

namespace App\Product\UseCase;

use App\Product\Entity\Category;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class CategoryService
{
    public function updateData(): void
    {
        $config = config('services.1c');
        try {
            $client = new Client([
                'base_uri' => $config['base_url'],
                'auth' => [$config['login'], $config['password']],
                'verify' => false
            ]);

            $response = $client->get($config['urls'][0]);
            $xml = simplexml_load_string($response->getBody()->getContents());
            if ($xml === false)
                throw new \DomainException('Ошибка парсинга xml');

            foreach ($xml->categories->category as $item) {
                $attr = $item->attributes();

                Category::updateOrCreate(
                    ['id' => (int) $attr->id],
                    ['name' => (string) $item, 'parent_id' => (int)$attr->parentId ? (int)$attr->parentId : null]
                );
            }
        } catch (\Exception | GuzzleException $e) {
            throw new \DomainException($e->getMessage());
        }
    }
}
