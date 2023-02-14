import Image from "next/image";
import { Row } from "react-bootstrap";
import Layout from "../templates";
import Page from "../components/page";
import Box from "../assets/images/content/box.svg";
import Delivery from "../assets/images/content/delivery.svg";
import delivery_0 from "../assets/images/delivery/i-delivery-0.png";
import delivery_1 from "../assets/images/delivery/i-delivery-1.png";
import delivery_2 from "../assets/images/delivery/i-delivery-2.png";
import delivery_3 from "../assets/images/delivery/i-delivery-3.png";
import delivery_4 from "../assets/images/delivery/i-delivery-4.png";
import { FC, useCallback } from "react";
import Breadcrumbs from "../components/breadcrumbs";

const DeliveryBooking: FC = () => {
  const getDefaultGenerator = useCallback(() => [
    { href: '/delivery-booking', text: "Доставка/Бронирование" }
  ], []);

  return (
    <Layout title="Доставка/Бронирование - Сеть аптек 120/80" description="Вы можете совершить покупку и забрать свой заказ самостоятельно, приехав в аптеку. Оплата при получении наличными или картой.">
      <Breadcrumbs getDefaultGenerator={getDefaultGenerator} />

      <Page title="Доставка/Бронирование">
        <Row>
          <div className="col-6 text-center">
            <Box width={60} height={60} />
            <p className="fw-bold">Самовывоз</p>
            <p>
              Вы можете совершить покупку и забрать свой заказ
              <br />
              самостоятельно, приехав в аптеку.
              <br />
              Оплата при получении наличными или картой.
            </p>
          </div>
          <div className="col-6 text-center">
            <Delivery width={60} height={60} />
            <p className="fw-bold">Доставка</p>
            <p>
              Доставка осуществляется с 9:00 до 21:00, без выходных.
              <br />
              По другим городам доставка осуществляется по таксометру.
              <br />
              Стоимость доставки по Махачкале от 2000&#8381; бесплатно.
            </p>
          </div>
          <div className="col-12 mt-3">
            <p>
              Согласно Указу Президента №187 от 17 марта 2020 года о
              дистанционной продажи безрецептурных лекарств осуществляется
              доставка на дом безрецептурных лекарственных средств, а также БАД,
              медицинских изделий, товаров для дома и красоты, бытовой химии и
              сопутствующих товаров.
            </p>
            <p>
              Заказать рецептурный препарат на сайте, можно только путем
              самовывоза из аптеки при наличии рецепта, выписанного врачом
            </p>
            <p>
              Информация о товаре, в том числе цена товара, носит
              ознакомительный характер и не является публичной офертой согласно
              ст.437 ГК РФ.
            </p>
          </div>
        </Row>

        <div className="delivery-box">
          <div className="delivery-item">
            <Image src={delivery_0} />
            <span>Мы работаем для вас без выходных!</span>
          </div>
          <div className="delivery-item">
            <Image src={delivery_1} />
            <span>Доставка лекарств из ближайшей аптеки</span>
          </div>
          <div className="delivery-item">
            <Image src={delivery_2} />
            <span>Бережная транспортировка надлежащих условиях</span>
          </div>
          <div className="delivery-item">
            <Image src={delivery_3} />
            <span>Звонок курьера перед доставкой</span>
          </div>
          <div className="delivery-item">
            <Image src={delivery_4} />
            <span>Доставка в удобный интервал времени</span>
          </div>
        </div>
        <p className="text-center">
          Оставайтесь дома! Заказывайте доставку! А мы бережно привезем все
          самое необходимое в удобное для вас время.
        </p>
      </Page>
    </Layout>
  );
};

export default DeliveryBooking;
