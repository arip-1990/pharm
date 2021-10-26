@extends('layouts.default')

@section('banner', '')

@section('content')
    <div class="row page">
        <div class="col-12 col-md-6">
            <div id="map" style="width: 100%; height: 400px"></div>
        </div>

        <div class="col-12 col-md-6">
            <h4 class="text-center">{{ $store->name }}</h4>

            @if (isset($store->route))
                <h5><b>Как добраться:</b></h5>
                <span>{!! $store->route !!}</span>
            @endif

            <h5><b>Режим работы:</b></h5>
            <span>{!! \App\Helper::formatSchedule($store->schedule, true) !!}</span>

            <h5><b>Доставка:</b></h5>
            <span>{{ $store->delivery ? 'Есть' : 'Нет' }}</span>

            <h5><b>Способ оплаты:</b></h5>
            <span>картой <img src="/images/payments.png" height="20px" alt="Мир, Visa, MasterCard, Maestro">, наличными</span>

            <h5><b>Контакты:</b></h5>
            <span>{{ $phone = $store->phone }}</span>
        </div>
    </div>
@endsection

@php
    $mapInfo = [];
    $coordinates = [];
    if ($store->lat and $store->lon) {
        $mapInfo[] = [$store->name, $store->phone];
        $coordinates[] = [$store->lat, $store->lon];
    }
@endphp

@section('scripts')
    <script src="https://api-maps.yandex.ru/2.1/?apikey=de8de84b-e8b4-46c9-ba10-4cf2911deebf&lang=ru_RU"></script>
    <script>
        ymaps.ready(function () {
            const map_info = {!! json_encode($mapInfo, JSON_UNESCAPED_UNICODE) !!};
            const myMap = new ymaps.Map('map', {
                center: [<?= $store->lat ?>, <?= $store->lon ?>],
                zoom: 17,
                behaviors: ['default', 'scrollZoom']
            }, {
                searchControlProvider: 'yandex#search'
            }),
            clusterer = new ymaps.Clusterer({
                preset: 'islands#invertedVioletClusterIcons',
                groupByCoordinates: false,
                clusterDisableClickZoom: true,
                clusterHideIconOnBalloonOpen: false,
                geoObjectHideIconOnBalloonOpen: false
            }),
            getPointData = (index) => ({
                balloonContentHeader: map_info[index][0],
                balloonContentBody: map_info[index][1],
            }),
            getPointOptions = () => ({preset: 'islands#violetIcon'}),
            points = {!! json_encode($coordinates, JSON_UNESCAPED_UNICODE) !!},
            geoObjects = [];

            for(let i = 0, len = points.length; i < len; i++) {
                geoObjects[i] = new ymaps.Placemark(points[i], getPointData(i), getPointOptions());
            }

            clusterer.options.set({
                gridSize: 80,
                clusterDisableClickZoom: true
            });
            clusterer.add(geoObjects);
            myMap.geoObjects.add(clusterer);
        });
    </script>
@endsection
