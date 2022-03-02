import { Container, Row, Col } from 'react-bootstrap';
import Navbar from './navbar';

export default () => {
    return (
        <Container as='header' className='my-3'>
            <Row>
                <Col xs={5} sm={5} className="menu-city">
                    <div>
                        {/* <span>Ваш город:</span>
                        @php $city = Illuminate\Support\Facades\Cookie::get('city', config('data.city')[0]) @endphp
                        <a className="dropdown-toggle" href="#" role="button" aria-expanded="false">{{ $city }}</a>
                        <ul className="dropdown-menu p-0">
                            @foreach (config('data.city') as $item)
                                <li>
                                    <a className="dropdown-item{{ $city == $item ? ' active': '' }}" href="{{ route('setCity', ['city' => $item]) }}">{{ $item }}</a>
                                </li>
                            @endforeach
                        </ul> */}
                    </div>

                    {/* <div className="city-choose" style="{{ (!Illuminate\Support\Facades\Cookie::has('city')) ? 'display: flex;' : '' }}">
                        <h5 className="w-100 mb-3">Ваш город {{ $city }}?</h5>
                        <a className="btn btn-sm btn-primary" href="{{ route('setCity', ['city' => $city]) }}">Да, все верно</a>
                        <button className="btn btn-sm btn-outline-secondary city-another">Выбрать другой</button>
                    </div> */}
                </Col>

                <Col xs={7} sm={7} className="auth text-end">
                    <span className="phone">+7 (8722) 606-366</span>
                    <span className="d-inline-block">
                        {/* @auth
                            <a href="{{ route('profile') }}">Личный кабинет</a> |
                            <a href="#" onclick="event.preventDefault();document.getElementById('logout-form').submit();">Выйти</a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        @else
                            <a href="{{ route('login') }}" data-toggle="modal" data-target="login">Вход</a> |
                            <a href="{{ route('register') }}" data-toggle="modal"  data-target="register">Регистрация</a>
                        @endauth */}
                    </span>
                </Col>
            </Row>

            <div className="empty-box"></div>
            <div className="fixed-box">
                <Container>
                    <Row className="align-items-center p-0 mx-auto">
                        <Col xs={6} sm={6} md={4} lg={3} className="me-auto me-lg-0">
                            <a href="/">
                                <img src="/images/logo.svg" alt="logo" className="logo" />
                            </a>
                        </Col>
                        <Col xs={{span: 12, order: 3}} sm={{span: 12, order: 3}} lg={{span: 7, order: 0}} className="mt-3 mt-lg-0">
                            <form className="search" action="{{ route('catalog.search') }}" autoComplete="off">
                                <input type="search" name="q" className="form-control" placeholder="Введите: название препарата, производителя, действующее вещество" />
                                <button type="submit" className="btn btn-primary btn-search">Найти</button>
                            </form>
                        </Col>
                        <Col xs={3} sm={2} lg={1} className="text-center" style={{marginTop: 19}}>
                            <a className="fav" href="/favorite">
                                <span style={{display: 'inline-block', position: 'relative'}}>
                                    {/* <span className="quantity">{{ count(session('favorites', [])) }}</span> */}
                                    <img src="/images/heart.png" height={30} />
                                </span>
                                <br />
                                <span>Избранное</span>
                            </a>
                        </Col>
                        <Col xs={3} sm={2} lg={1} className="text-center" style={{marginTop: 19}}>
                            <a className="cart" href="/cart">
                                <span style={{display: 'inline-block', position: 'relative'}}>
                                    {/* <span className="quantity">{{ $cartService->getTotal() }}</span> */}
                                    <img src="/images/cart.png" height={30} />
                                </span>
                                <br />
                                <span>Корзина</span>
                            </a>
                        </Col>
                    </Row>
                </Container>
            </div>

            <Row>
                <Navbar />
            </Row>
        </Container>
    );
}
