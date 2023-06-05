<?php

namespace App\Services\DaData;

class CleanClient extends ClientBase
{
    public function __construct(string $token, string $secret)
    {
        parent::__construct(config('dadata.cleaner_url'), $token, $secret);
    }

    public function clean($name, $value)
    {
        $url = "clean/$name";
        $fields = [$value];
        $response = $this->post($url, $fields);
        return $response[0];
    }

    public function cleanRecord($structure, $record)
    {
        $url = "clean";
        $data = [
            "structure" => $structure,
            "data" => [$record]
        ];
        $response = $this->post($url, $data);

        return $response["data"][0];
    }
}
