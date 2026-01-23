import { FC, useCallback, useState } from "react";
import Link from "next/link";
import Image from "next/image";

import Layout from "../templates";
import Page from "../components/page";
import Breadcrumbs from "../components/breadcrumbs";
import Auth from "../components/auth";
import { Col, Row } from "react-bootstrap";

import spravkaBanner from "../assets/images/spravka.jpg";
import classNames from "classnames";

const Loyalty: FC = () => {
  const [showModal, setShowModal] = useState<boolean>(false);
  const [scaleBanner, setScaleBanner] = useState<boolean>(false);

  const getDefaultGenerator = useCallback(
    () => [{ href: "/loyalty", text: "Программа лояльности" }],
    []
  );

  return (
    <Layout
      title="Программа лояльности - Сеть аптек 120/80"
      description="Мы заинтересованы в активном развитии нашей сети. Просим Вас внимательно ознакомиться с требованиями, предъявляемыми нами к потенциальным помещениям."
    >
      <Breadcrumbs getDefaultGenerator={getDefaultGenerator} />

      <Page title="Программа лояльности">
        <Row>
          <Col xs={9}>
            <span>
              Получить бонусную карту можно <b>бесплатно</b> при совершении любой покупки в аптеке, регистрации на сайте или в мобильном приложении.
            </span>
            <br/>
            <br/>
            <span>
              Активируйте карту сразу, в момент покупки, и уже на следующий день вы сможете воспользоваться подарочными 100 бонусами = 100 руб.
            </span>
            <br/>
            <br/>
            <span>
              Не забывайте предъявлять карту при каждой покупке в аптеке и накапливайте бонусы: 1% от суммы чека в любой день и 12% каждый вторник.
            </span>
            <br/>
            <br/>
            <span>
              Дополнительно ежемесячно мы готовим более 200 позиций, за покупки которых на вашу карту вернется 6% или 10% от их стоимости. Подробнее со списком акционных товаров вы можете ознакомиться на сайте в раздели Акции https://120на80.рф/stock
            </span>
            <ul>
              <br/>
              <li>
                1 начисленный бонус = 1 рублю
              </li>
              <br/>
              <li>
                За одну покупку вы можете или начислить бонусы, или списать.
              </li>
              <br/>
              <li>
                За одну покупку вы можете оплатить до 90% от суммы чека бонусами.
              </li>
              <br/>
              {/*<li>*/}
              {/*  Оплата товаров баллами возможна до <b>99</b>% от суммы чека,*/}
              {/*  минимальная сумма для списания не ограничена;*/}
              {/*</li>*/}
              {/*<br/>*/}
              {/*<li>*/}
              {/*  На товары участвующие в акциях действует{" "}*/}
              {/*  <b>повышенный процент накопления бонусов</b>**/}
              {/*</li>*/}
            </ul>
            <small style={{display: "inline-block", marginTop: "2rem"}}>
              *Акционные позиции уточняйте у фармацевтов, либо на нашем сайте{" "}
              <Link href="/">
                <a>
                  <b>https://120на80.рф</b>
                </a>
              </Link>{" "}
              в разделе «<b>АКЦИИ</b>»
            </small>
          </Col>
          <Col
              xs={3}
            style={{ position: "relative" }}
            className={classNames({ "scale-1_5": scaleBanner })}
          >
            {/*<Image*/}
            {/*  style={{ cursor: "pointer" }}*/}
            {/*  itemProp="image"*/}
            {/*  layout="fill"*/}
            {/*  objectFit="contain"*/}
            {/*  src={spravkaBanner}*/}
            {/*  alt=""*/}
            {/*  onClick={() => setScaleBanner((old) => !old)}*/}
            {/*/>*/}
          </Col>
        </Row>
      </Page>

      <Auth
        show={showModal}
        type="register"
        onHide={() => setShowModal(false)}
      />
    </Layout>
  );
};

export default Loyalty;
