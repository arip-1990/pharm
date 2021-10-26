@extends('layouts.default')

@section('banner', '')

@section('content')
    <h3 class="text-center">Точки самовывоза</h3>

    <div class="page">
        <div id="map" style="width: 100%; height: 400px"></div>
            @php
                $mapInfo = [];
                $cordinates = [];
            @endphp
            <?php /** @var App\Entities\Store $store */ ?>
            @foreach ($paginator as $store)
                <div class="row address">
                    <div class="col-12 col-md-5 text-center text-md-start">
                        <span>{{ trim(str_replace(explode(',', $store->address)[0] . ',', '', $store->name)) }}</span>
                    </div>
                    <div class="col-12 col-md-4 col-lg-3 text-center">
                        {!! \App\Helper::formatSchedule($store->schedule) !!}
                    </div>
                    <div class="col-12 col-md-3 col-lg-2 text-center text-md-end">
                        {{ \App\Helper::formatPhone($store->phone) }}
                    </div>
                    <div class="col-12 col-lg-2 d-flex justify-content-between position-relative">
                        <div class="{{ $store->delivery ? 'store-delivery_icon' : '' }}"></div>
                        <a href="{{ route('pharmacy.show', ['id' => $store->id]) }}" class="btn btn-sm btn-primary">Посмотреть</a>
                    </div>
                </div>

                @php
                    if ($store->lat and $store->lon) {
                        $cordinates[] = [$store->lat, $store->lon];
                        $mapInfo[] = [$store->name, $store->phone];
                    }
                @endphp
            @endforeach
    </div>

    {{ $paginator->links('layouts.partials.pagination') }}
@endsection

@section('scripts')
    <script src="https://api-maps.yandex.ru/2.1/?apikey=de8de84b-e8b4-46c9-ba10-4cf2911deebf&lang=ru_RU"></script>
    <script>
        ymaps.ready(function () {
            const mapInfo = {!! json_encode($mapInfo, JSON_UNESCAPED_UNICODE) !!};
            const myMap = new ymaps.Map('map', {
                center: [42.961079, 47.534646],
                zoom: 11,
                behaviors: ['default', 'scrollZoom']
            },
            {
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
                balloonContentHeader: mapInfo[index][0],
                balloonContentBody: mapInfo[index][1]
            }),
            getPointOptions = () => ({preset: 'islands#violetIcon'}),
            points = {!! json_encode($cordinates, JSON_UNESCAPED_UNICODE) !!},
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
            myMap.setBounds(clusterer.getBounds(), {
                checkZoomRange: true
            });
        });
    </script>
@endsection
