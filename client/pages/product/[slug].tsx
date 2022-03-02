import { useRouter } from 'next/router';
import { Col, Row } from 'react-bootstrap';
import axios from 'axios';
import { IProduct } from '../../models/IProduct';

interface PropsType {
    data: IProduct;
}

const Product = ({data}: PropsType) => {
    return (
        <>
            <Row className="justify-content-center mb-3" itemScope itemType="https://schema.org/Product">
                <Col xs={8} sm={7} md={5} lg={3} className="position-relative">
                    <img className="mw-100" itemProp="image" src={data.photos[0]} alt={data.name} />

                    {/* @if (in_array($product->id, session('favorites', [])))
                        <img alt="" src="/images/heart.png" style="left: 1.5rem" className="favorite-toggle" data-action="remove" />
                    @else
                        <img alt="" src="/images/fav.png" style="left: 1.5rem" className="favorite-toggle" data-action="add" />
                    @endif */}
                </Col>

                <Col xs={12} sm={12} lg={9} className="d-flex flex-column justify-content-around">
                    <h4 className="text-center" itemProp="name">{data.name}</h4>

                    <Row style={{minHeight: '50%'}}>
                        <Col xs={12} sm={12} lg={8} xxl={9} className="mb-3 mb-lg-0">
                            {/* @if ($product->values()->count())
                                <div style={{background: '#e6eded', padding: '0.75rem'}}>
                                    @foreach ($product->values as $value)
                                        @switch ($value->attribute->name)
                                            @case('Производитель')
                                            @case('Страна')
                                            @case('Действующее вещество')
                                            @case('Условия отпуска из аптек')
                                            <h6>
                                                <b className="me-2">{{ $value->attribute->name }}:</b>
                                                {{ html_entity_decode($value->value) }}
                                            </h6>
                                        @endswitch
                                    @endforeach
                                    <h6>
                                        <b className="collapsed description-info" data-bs-toggle="collapse" data-bs-target="#product-desc">Информация о товаре</b>
                                    </h6>
                                </div>
                            @endif */}
                        </Col>
                        <Col xs={12} sm={12} lg={4} xxl={3} className="d-flex flex-column justify-content-between align-items-end">
                            {/* @if ($minPrice)
                                <h4 className="text-end price" itemProp="offers" itemSope itemtype="https://schema.org/Offer">
                                    <p className="mask">Показать цену</p>
                                    <p className="real" itemProp="price"></p>
                                </h4>

                                @if($item)
                                    <div className="input-group input-product">
                                        <button className="btn btn-outline-primary" data-type="-">-</button>
                                        <input type="number" className="form-control input-number" min="1" max="{{ $product->getCount() }}" value="{{ $item->quantity }}" />
                                        <button className="btn btn-outline-primary" data-type="+">+</button>
                                    </div>
                                @else
                                    <a className="btn btn-primary" data-toggle="modal" data-target="product" data-max="{{ $product->getCount() }}">
                                        Добавить в корзину <i className="fas fa-caret-right" style={{verticalAlign: 'middle'}}></i>
                                    </a>
                                @endif
                            @else
                                <h4 className="text-center">Нет в наличии</h4>
                            @endif */}
                        </Col>
                    </Row>
                </Col>
            </Row>

            <div id="product-desc" className="description collapse">
                {data.description &&
                    <div className="description-item">
                        <h6 className="description-item_title collapsed" data-bs-toggle="collapse" data-bs-target="#collapse-1">Описание</h6>
                        <div id="collapse-1" className="collapse" data-bs-parent="#product-desc">
                            <div className="description-item_body" itemProp="description">{data.description}</div>
                        </div>
                    </div>
                }

                {/* @foreach ($product->values as $i => $value)
                    @if ($value->value)
                        <div className="description-item">
                            <h6 className="description-item_title collapsed" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $i + 2 }}">{{ $value->attribute->name }}</h6>
                            <div id="collapse-{{ $i + 2 }}" className="collapse" data-bs-parent="#product-desc">
                                <div className="description-item_body">{!! html_entity_decode($value->value) !!}</div>
                            </div>
                        </div>
                    @endif
                @endforeach */}
            </div>

            {/* @if ($minPrice) */}
                <Row className="p-2 fw-bold d-md-flex m-0" style={{display: 'none', background: '#f4f4f4', color: '#757a7a'}}>
                    <Col md={5} className="text-center">Адрес</Col>
                    <Col md={3} className="text-center">Время работы</Col>
                    <Col md={2} className="text-center">Цена</Col>
                    <Col md={2} className="text-center">Количество</Col>
                </Row>

                {/* @foreach ($offers as $offer)
                    @if ($offer->store)
                        <Row className="align-items-center border-top p-2 m-0">
                            <Col xs={12} sm={12} md={5}>
                                <b>{{ $offer->store->name }}</b>
                            </Col>
                            <Col xs={12} sm={12} md={3} className="text-md-center">
                                <b className="d-md-none">Время работы: </b>{!! \App\Helper::formatSchedule($offer->store->schedule) !!}
                            </Col>
                            <Col xs={12} sm={12} md={2} className="text-md-center">
                                <b className="d-md-none">Цена: </b>{{ $offer->price }} &#8381;
                            </Col>
                            <Col xs={12} sm={12} md={2} className="text-md-center">
                                <b className="d-md-none">Количество:</b>
                                @if($offer->quantity >= 10)
                                    много
                                @else
                                    {{ $offer->quantity }} шт.
                                @endif
                            </Col>
                        </Row>
                    @else
                        {{ $offer->store_id }}
                    @endif
                @endforeach */}
            {/* @endif */}
        </>
    );
}

export const getServerSideProps = async ({params}) => {
    const { data } = await axios.get(`/product/${params.slug}`);

    return { props: { data } };
}

export default Product;
