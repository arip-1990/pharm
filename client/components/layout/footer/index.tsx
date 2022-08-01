import Link from "next/link";
import { Container, Row, Col } from "react-bootstrap";
import moment from "moment";
import Logo from "../../../assets/images/logo_min.svg";
import { FC } from "react";
import styles from "./Footer.module.scss";

const Footer: FC = () => {
  return (
    <Container as="footer" className={styles.footer}>
      <Row className={styles.ofer}>
        <span>
          Информация о товаре, в том числе цена товара, носит ознакомительный
          характер и не является публичной офертой согласно ст.437 ГК РФ.
        </span>
      </Row>

      <Row className={styles.info}>
        <Col
          sm={{ span: 10, offset: 1 }}
          md={{ span: 6, offset: 0 }}
          className="ps-md-5 d-flex align-items-center"
        >
          <Logo className={styles.info_logo} />
          <div className={styles.info_phone}>
            <h5>Единая справочная сети</h5>
            <p>+7 (8722) 606-366</p>
            <p className={styles.times}>ежедневно с 9:00 до 21:00</p>
          </div>
        </Col>
        <Col
          xs={{ span: 10, offset: 1 }}
          sm={{ span: 5, offset: 1 }}
          md={{ span: 3, offset: 0 }}
        >
          <ul>
            <li>
              <h4>
                <Link href="/about">
                  <a>О компании</a>
                </Link>
              </h4>
            </li>
            <li>
              <Link href="/advantage">
                <a>Наши преимущества</a>
              </Link>
            </li>
            <li>
              <Link href="/store">
                <a>Адреса аптек</a>
              </Link>
            </li>
            <li>
              <Link href="/rent">
                <a>Развитие сети/Аренда</a>
              </Link>
            </li>
            <li>
              <Link href="/rules-remotely">
                <a>Правила дистанционной торговли ЛС</a>
              </Link>
            </li>
            <li>
              <Link href="/return">
                <a>Условия возврата</a>
              </Link>
            </li>
          </ul>
        </Col>
        <Col
          xs={{ span: 10, offset: 1 }}
          sm={{ span: 5, offset: 0 }}
          md={3}
          className="mt-3 mt-sm-0"
        >
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
            <li>
              <a
                href="{{ route('register') }}"
                data-toggle="modal"
                data-target="register"
              >
                Регистрация
              </a>
            </li>
            <li>
              <Link href="/favorite">
                <a>Отложенные товары</a>
              </Link>
            </li>
            <li>
              <Link href="/processing-personal-data">
                <a>Обработка персональных данных</a>
              </Link>
            </li>
            <li>
              <Link href="/privacy-policy">
                <a>Политика конфиденциальности</a>
              </Link>
            </li>
            <li>
              <Link href="/order-payment">
                <a>Оплата заказа</a>
              </Link>
            </li>
          </ul>
        </Col>
        <Col xs={9} sm={9} className="mt-3 mt-md-0">
          <p style={{ fontSize: "0.75rem", margin: 0 }}>
            ООО «Социальная аптека»;
            <br />
            Адрес: Республика Дагестан, г. Махачкала, пр. Гамидова, дом 48;{" "}
            <br />
            Лицензия: № ЛО-05-02-001420 от 27 декабря 2019 г.; <br />
            ИНН 0571008484; ОГРН: 1160571061353
          </p>
        </Col>
        <p
          className="col-3 text-end align-self-end m-0"
          style={{ fontSize: "0.8rem", letterSpacing: 2 }}
        >
          &copy;{moment().format("YYYY")}
        </p>
      </Row>

      <Row>
        <div className="text-center box-warning">
          ИМЕЮТСЯ ПРОТИВОПОКАЗАНИЯ. НЕОБХОДИМА КОНСУЛЬТАЦИЯ СПЕЦИАЛИСТА.
        </div>
      </Row>
    </Container>
  );
};

export default Footer;
