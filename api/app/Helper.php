<?php

namespace App;

use \App\Models\Status;

class Helper
{
    public static function formatSchedule(array $schedule, bool $long = false): string
    {
        $str = '';
        if ($long) {
            $week = ['Понедельник','Вторник','Среда','Четверг','Пятница','Суббота','Воскресенье'];
            foreach ($schedule as $key => $value) {
                $str .= $week[$key] . ' ';

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

    public static function formatPhone(?string $phone, bool $mobile = false): string
    {
        $tmp = '';
        if ($phone) {
            if ($mobile) {
                $phone = substr_replace($phone, ') ' . substr($phone, 4), 4);
                $phone = substr_replace($phone, ' (' . substr($phone, 1), 1);
                $phone = substr_replace($phone, '-' . substr($phone, -4), -4);
                $phone = substr_replace($phone, '-' . substr($phone, -2), -2);
                $tmp = '+' . $phone;
            }
            else {
                $phone = str_replace('8722', ' (8722) ', $phone);
                $tmp = substr_replace($phone, '-' . substr($phone, -3), -3);
            }
        }

        return $tmp;
    }

    public static function getCoordinates(string $geoCode): array
    {
        $apiKey = config('data.yandex.GeoCoder.apikey');
        if(empty($apiKey))
            throw new \Exception('Не задан api ключ для GeoCoder');

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
        if(!isset($json['response']))
            throw new \Exception('Не удалось получить ответ от GeoCoder');

        $coordinates = $json['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['Point']['pos'];
        $coordinates = explode(' ', $coordinates);
        $lon = $coordinates[0];// долгота
        $lat = $coordinates[1];// широта

        return ['lon' => (float)$lon, 'lat' => (float)$lat];
    }

    public static function getStatusInfo(string $status): string
    {
        return match ($status) {
            Status::STATUS_ACCEPTED => '<span class="text-success">Принят</span>',
            Status::STATUS_ASSEMBLED_PHARMACY => '<span class="text-success">Заказ собран</span>',
            Status::STATUS_RECEIVED_BY_CLIENT => '<span class="text-success">Заказ получен</span>',
            Status::STATUS_CAUSED_BY_DELIVERY => '<span class="text-muted">Вызвана доставка</span>',
            Status::STATUS_CANCELLED, Status::STATUS_DISBANDED => '<span class="text-danger">Отменен</span>',
            Status::STATUS_FULL_REFUND, Status::STATUS_RETURN_BY_COURIER => '<span class="text-danger">Возврат</span>',
            default => '<span class="text-muted">В обработке</span>',
        };
    }
}
