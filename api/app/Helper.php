<?php

namespace App;

use Illuminate\Support\Collection;
use JetBrains\PhpStorm\ArrayShape;

class Helper
{
    public static function formatSchedule(Collection $schedule, bool $long = false): string
    {
        $str = '';
        if ($long) {
            $week = ['Понедельник','Вторник','Среда','Четверг','Пятница','Суббота','Воскресенье'];
            foreach ($schedule as $key => $value) {
                $str .= $week[$key] . ': ';

                if($value['open'] == $value['close'])
                    $str .= 'Круглосуточно';
                else
                    $str .= 'с ' . $value['open'] . ' до ' . $value['close'];

                $str .= '<br>';
            }
        }
        else {
            $week = ['Пн','Вт','Ср','Чт','Пт','Сб','Вс'];
            $last = ['open' => '', 'close' => ''];
            $periods = [];
            $index = -1;
            foreach ($schedule as $key => $value) {
                if($last['open'] == $value['open'] && $last['close'] == $value['close']) {
                    $periods[$index]['end'] = $key;
                }
                else{
                    $index++;
                    $periods[$index]['begin'] = $key;
                    $periods[$index]['open'] = $value['open'];
                    $periods[$index]['close'] = $value['close'];
                }

                $last = ['open' => $value['open'], 'close' => $value['close']];
            }

            $roundClock = 0;
            foreach ($periods as $day) {
                $str .= $week[$day['begin']] . (isset($day['end']) ? '-' . $week[$day['end']] : '') . ': ';

                if($day['open'] == $day['close']) {
                    $str .= 'Круглосуточно';
                    $roundClock++;
                }
                else
                    $str .= 'с ' . $day['open'] . ' до ' . $day['close'];

                $str .= '<br>';
            }

            if (count($periods) === $roundClock)
                $str = 'Круглосуточно';
        }

        return $str;
    }

    public static function formatPhone(?string $phone): ?string
    {
        if (!$phone) return null;

        $phone = explode('|', $phone);
        $patterns = ['/^7(\d{3})(\d{3})(\d{2})(\d{2})$/', '+7 ($1) $2-$3-$4'];
        if (strpos($phone[0], '8722', 1) === 1) $patterns = ['/^7(8722)(\d{3})(\d{3})$/', '+7 ($1) $2-$3'];
        if (!empty($phone[1])) $patterns[1] = $patterns[1] . ', доб.' . $phone[1];

        return preg_replace($patterns[0], $patterns[1], $phone[0]);
    }

    #[ArrayShape(['lon' => "float", 'lat' => "float"])]
    public static function getCoordinates(string $geoCode): array
    {
        $apiKey = config('data.yandex.GeoCoder.apikey');
        if(empty($apiKey)) throw new \DomainException('Не задан api ключ для GeoCoder');

        $data = [
            'apikey'    => $apiKey,
            'format'    => 'json',
            'geocode'   => $geoCode,
        ];
        $url = 'https://geocode-maps.yandex.ru/1.x?';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        $json = json_decode($response, true);
        if(!isset($json['response'])) throw new \DomainException('Не удалось получить ответ от GeoCoder');

        $coordinates = $json['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['Point']['pos'];
        $coordinates = explode(' ', $coordinates);
        $lon = $coordinates[0];// долгота
        $lat = $coordinates[1];// широта

        return ['lon' => (float)$lon, 'lat' => (float)$lat];
    }

    public static function trimPrefixCity(string $city): ?string
    {
        return preg_replace('/^(г|пос|мкр|пгт|с)[. ]+/', '', trim($city));
    }

    public static function trimPrefixStreet(string $street): ?string
    {
        return preg_replace('/^(пр|ул|д)[. ]+/', '', trim($street));
    }
}
