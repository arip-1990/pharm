import { Container, Row, Col } from "react-bootstrap";
import moment from 'moment';

export default () => {
    return (
        <Container as='footer'>
            <Row className="ofer">
                <span>Информация о товаре, в том числе цена товара, носит ознакомительный характер и не является публичной офертой согласно ст.437 ГК РФ.</span>
            </Row>

            <Row className="footer">
                <Col sm={{span: 10, offset: 1}} md={{span: 6, offset: 0}} className="ps-md-5 d-flex align-items-center">
                    <img className="logo" src='/images/logo_min.svg' alt="logo" />
                    <div className="info-phone text-center">
                        <h5>Единая справочная сети</h5>
                        <p>+7 (8722) 606-366</p>
                        <p className="times">ежедневно с 9:00 до 21:00</p>
                    </div>
                </Col>
                <Col xs={{span: 10, offset: 1}} sm={{span: 5, offset: 1}} md={{span: 3, offset: 0}}>
                    <ul>
                        <li><h4><a href="{{ route('about') }}">О компании</a></h4></li>
                        <li><a href="{{ route('advantage') }}">Наши преимущества</a></li>
                        <li><a href="{{ route('pharmacy') }}">Адреса аптек</a></li>
                        <li><a href="{{ route('rent') }}">Развитие сети/Аренда</a></li>
                        <li><a href="{{ route('rulesRemotely') }}">Правила дистанционной торговли ЛС</a></li>
                        <li><a href="{{ route('return') }}">Условия возврата</a></li>
                    </ul>
                </Col>
                <Col xs={{span: 10, offset: 1}} sm={{span: 5, offset: 0}} md={3} className="mt-3 mt-sm-0">
                    <ul>
                        <li>
                            <h4>
                                {/* @auth()
                                    <a href="{{ route('profile') }}">Личный кабинет</a>
                                @else
                                    <a href="{{ route('profile') }}" data-toggle="modal" data-target="login">Личный кабинет</a>
                                @endauth */}
                            </h4>
                        </li>
                        <li><a href="{{ route('register') }}" data-toggle="modal"  data-target="register">Регистрация</a></li>
                        <li><a href="{{ route('favorite') }}">Отложенные товары</a></li>
                        <li><a href="{{ route('processingPersonalData') }}">Обработка персональных данных</a></li>
                        <li><a href="{{ route('privacyPolicy') }}">Политика конфиденциальности</a></li>
                        <li><a href="{{ route('orderPayment') }}">Оплата заказа</a></li>
                    </ul>
                </Col>
                <Col xs={9} sm={9} className="mt-3 mt-md-0">
                    <p style={{fontSize: '0.75rem', margin: 0}}>
                        ООО «Социальная аптека»;<br />
                        Адрес: Республика Дагестан, г. Махачкала, пр. Гамидова, дом 48; <br />
                        Лицензия: № ЛО-05-02-001420 от 27 декабря 2019 г.; <br />
                        ИНН 0571008484; ОГРН: 1160571061353
                    </p>
                </Col>
                <p className="col-3 text-end align-self-end m-0" style={{fontSize: '0.8rem', letterSpacing: 2}}>&copy;{moment().format('YYYY')}</p>
            </Row>

            <Row>
                <div className="text-center box-warning">
                    ИМЕЮТСЯ ПРОТИВОПОКАЗАНИЯ. НЕОБХОДИМА КОНСУЛЬТАЦИЯ СПЕЦИАЛИСТА.
                </div>
            </Row>
        </Container>
    );
}
