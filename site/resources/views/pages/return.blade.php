@extends('layouts.default')

@section('banner', '')

@section('content')
    {{ \Diglactic\Breadcrumbs\Breadcrumbs::render('return') }}

    <h3 class="text-center">Условия возврата</h3>

    <div class="page">
        <p>Клиент в праве на обмен или возврат раннее заказанного товара в случаях:</p>
        <ul>
            <li>Товар полностью не соответствует заказу;</li>
            <li>Срок годности уже закончился;</li>
            <li>Нет информации о производителе, форме выпуска, дате выпуска и пр.;</li>
            <li>Срок годности на самой пластинке с таблетками или ампулах отличается от того, который нанесен на первичную упаковку;</li>
            <li>Содержимое не соответствует по цвету, запаху, и пр. свойствам тем характеристикам, которые описаны в инструкции к препарату;</li>
            <li>Упаковка повреждена – есть скол. трещина, картонка измята, разорвана, нет крышки, и т. п.;</li>
            <li>Инструкция вовсе отсутствует или вложена на совершенно иной товар, и т. д.</li>
        </ul>

        <p class="text-center fw-bold">Товар может быть возвращен только до момента оплаты заказа.</p>
        <p>Согласно Постановлению Правительства РФ от 31.12.2020 г. №2463 купленный ранее товар, надлежащего качества, обмену и возврату не подлежит.</p>
        <ul>
            <li>лекарственные средства;</li>
            <li>предметы личной гигиены, средства гигиены полости рта;</li>
            <li>инструменты, приборы и аппаратура медицинские, предметы санитарии и гигиены из металла, резины, текстиля и других материалов;</li>
            <li>предметы по уходу за детьми;</li>
            <li>линзы очковые;</li>
            <li>парфюмерно-косметические товары.</li>
        </ul>
        <p>Возврат денежных средств ранее оплаченного товара осуществляется посредством возврата стоимости на банковскую карту, с которой была осуществлена оплата.</p>
    </div>
@endsection
