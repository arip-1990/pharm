import Link from 'next/link';
import { Row, Col, Card } from 'react-bootstrap';
import Layout from '../components/layout';
import { IProduct } from '../models/IProduct';
import { useEffect, useState } from 'react';
import axios from '../services/api';

interface PropsType {
    data: IProduct[];
}

const Home = ({data}: PropsType) => {
    const [products, setProducts] = useState<IProduct[]>([]);

    useEffect(() => {
        const fetcPRoducts = async () => {
            try {
                const { data } = await axios.get<IProduct[]>('/product/popular');
                setProducts(data);
            }
            catch (error: any) {
                console.log(error);
            }
        }
        fetcPRoducts();
    }, []);

  return (
    <Layout>
      <Row xs={{cols: 1}} sm={{cols: 2}} md={{cols: 3}} lg={{cols: 4}} className="g-3 g-lg-4" itemScope itemType="https://schema.org/ItemList">
        <link itemProp="url" href="{{ url()->current() }}" />
        { products.map(item => (
            <Col xs={{span: 10, offset: 1}} sm={{span: 10, offset: 0}}>
                <Card  className="product" itemProp="itemListElement" itemScope itemType="https://schema.org/Product">
                    {/* @if ('По рецепту' === $value = $product->getValue(4))
                        <div className="card-mod card-mod__prescription">
                            <div className="card-mod_icon" />
                            <div className="card-mod_text">По рецепту</div>
                        </div>
                    @elseif ('По назначению врача' === $value)
                        <div className="card-mod card-mod__appointment">
                            <div className="card-mod_icon" />
                            <div className="card-mod_text">По назначению врача</div>
                        </div>
                    @else
                        <div className="card-mod card-mod__delivery">
                            <div className="card-mod_icon" />
                            <div className="card-mod_text">Доставка</div>
                        </div>
                    @endif */}
                    <div className="card_img">
                        <img className="mt-2" itemProp="image" src={item.photos[0]} alt={item.name} />
                    </div>
                    {/* @if (in_array($product->id, session('favorites', [])))
                        <img alt="" src="/images/heart.png" className="favorite-toggle" data-action="remove" />
                    @else
                        <img alt="" src="/images/fav.png" className="favorite-toggle" data-action="add" />
                    @endif */}
                    <Card.Body>
                        <Card.Title>
                            <Link href={`product/${item.slug}`}>
                                <a className="product-link" itemProp="url">
                                    <span itemProp="name">{item.name}</span>
                                </a>
                            </Link>
                        </Card.Title>
                        <Card.Text>
                            {/* @if ($count = $product->getCountByCity($city))
                                <p className="marker">
                                    <i className="fas fa-map-marker-alt"></i>
                                    {{ "В наличии в $count " . ($count === 1 ? 'аптеке' : 'аптеках') }}
                                </p>
                                <div className="price" itemProp="offers" itemScope itemType="https://schema.org/Offer">
                                    <p className="mask">Показать цену</p>
                                    <p className="real" itemProp="price"></p>
                                </div>
                            @else
                                <p className="marker marker__red"><i className="fas fa-map-marker-alt" /> Нет в наличии</p>
                            @endif

                            @if($cartService->getItems()->contains(fn(\App\Entities\CartItem $item) => $item->product_id === $product->id))
                                <a className="btn btn-primary">Добавлено</a>
                            @else
                                <a className="btn btn-primary" data-toggle="modal" data-target="product" data-max="{{ $product->getCount() }}">
                                    Добавить в корзину <i className="fas fa-caret-right" style="vertical-align: middle" />
                                </a>
                            @endif */}
                        </Card.Text>
                    </Card.Body>
                </Card >
            </Col>
        ))}
    </Row>
    </Layout>
  )
}

export async function getServerSideProps() {
    try {
        const { data } = await axios.get<IProduct[]>('/product/popular');
        console.log(data);
    }
    catch (error) {
        console.log(error);
    }

    return { props: { data: [] } };
}

export default Home;
