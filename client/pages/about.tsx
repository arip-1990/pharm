import Image from "next/image";
import { Row } from "react-bootstrap";
import Layout from "../components/layout";
import Page from "../components/page";
import distance_trading from "../assets/images/licenses/distance_trading.jpg";
import license_1 from "../assets/images/licenses/license_1.jpg";
import license_2 from "../assets/images/licenses/license_2.jpg";
import license_3 from "../assets/images/licenses/license_3.jpg";
import license_4 from "../assets/images/licenses/license_4.jpg";
import license_5 from "../assets/images/licenses/license_5.jpg";
import license_6 from "../assets/images/licenses/license_6.jpg";
import { FC, useCallback } from "react";
import Breadcrumbs from "../components/breadcrumbs";

const About: FC = () => {
  const getDefaultGenerator = useCallback(() => [
    { href: '/about', text: "О компании" }
  ], []);

  return (
    <Layout title="О компании - Сеть аптек 120/80" description="Мы создаем новые стандарты обслуживания, внедряем новые технологии, стремимся удовлетворить запросы всех групп потребителей.">
      <Breadcrumbs getDefaultGenerator={getDefaultGenerator} />

      <Page>
        <h6
          className="text-center fw-bold mb-3"
          style={{ fontSize: "0.85rem" }}
        >
          <p>
            Мы создаем новые стандарты обслуживания, внедряем новые технологии,
            стремимся удовлетворить запросы всех групп потребителей.
          </p>
          <p>
            Профессиональный подход сотрудников аптек позволяет покупателям
            выбирать наиболее подходящие средства.
          </p>
          <p>
            Не секрет, что в аптеку люди ходят чаще, чем к врачу. Для вас мы
            создаём уютное пространство, в котором каждый может быстро получить
            квалифицированную консультацию и выбрать то, что ему необходимо.
          </p>
        </h6>

        <p>
          Добро пожаловать на наш сайт - сервис для покупки лекарств и товаров в
          собственной аптечной сети! Наши аптеки популярны, благодаря широкому
          ассортименту и высокой культуре обслуживания при доступных ценах.
          Гарантия качества и сервисное обслуживание – основные принципы нашей
          работы!
        </p>
        <p>
          Постоянно пополняющийся ассортимент, ориентированный на последние
          достижения на фармацевтическом рынке, где вы сможете найти в том числе
          редкие препараты, а также наименования парафармацевтических товаров,
          новинки из мира красоты и здоровья.
        </p>
        <p>
          Мы закупаем продукцию напрямую у ведущих компаний, специализирующихся
          на производстве и дистрибуции товаров для красоты и здоровья. Наличие
          собственного крупного склада, позволяет иметь возможность соблюдать
          все условия хранения лекарственных препаратов в соответствии со всеми
          установленными производителем требованиями и нормами. Все препараты
          сертифицированы, в связи с этим исключена любая возможность появления
          фальсификатов. Именно поэтому мы даём нашим покупателям 100% гарантию
          качества!
        </p>
        <p>
          Квалифицированные специалисты – провизоры и фармацевты готовы в любой
          момент оказать помощь в подборе препаратов, витаминов, ортопедических
          товаров, товаров медицинского назначения; проконсультировать о
          новинках косметических средств ведущих мировых брендов и многого
          другого.
        </p>
        <p>
          Не секрет, что в аптеку люди ходят чаще, чем к врачу. Для вас, мы
          создаём уютное пространство, в котором каждый может быстро получить
          квалифицированную консультацию и выбрать то, что ему необходимо.
        </p>
        <p>Ваша улыбка – наша главная награда за ежедневный труд!</p>
        <p>
          Здоровье невозможно купить, но можно купить то, что его может
          поддержать! Посещая наши аптеки, вы экономите личное время и средства
          на приобретение нужных лекарственных средств!
        </p>

        <ul>
          <li>
            Наши аптеки популярны, благодаря широкому ассортименту и высокой
            культуре обслуживания при доступных ценах.
          </li>
          <li>
            Гарантия качества и сервисное обслуживание – основные принципы нашей
            работы!
          </li>
          <li>
            Постоянно пополняющийся ассортимент насчитывает сегодня более 30 000
            наименований лекарственных препаратов, в том числе редких, а также
            наименования парафармацевтических товаров.
          </li>
        </ul>

        <p className="text-center fw-bold">
          <span className="border-bottom border-primary">
            Здоровье невозможно купить, но можно купить то, что его может
            поддержать! Ваше здоровье – наша забота!
          </span>
        </p>

        <Row>
          <div className="col-5 offset-lg-4 text-center">
            <p className="text-center fw-bold">
              Разрешение на дистанционную торговлю:
            </p>
            <a href="/images/licenses/distance_trading.jpg">
              <Image src={distance_trading} alt="Лицензия" />
            </a>
          </div>
        </Row>

        <p className="fw-bold">Лицензии:</p>
        <Row>
          <div className="col-4">
            <a href="/images/licenses/license_1.jpg">
              <Image src={license_1} alt="Лицензия" />
            </a>
          </div>
          <div className="col-4">
            <a href="/images/licenses/license_2.jpg">
              <Image src={license_2} alt="Лицензия" />
            </a>
          </div>
          <div className="col-4">
            <a href="/images/licenses/license_3.jpg">
              <Image src={license_3} alt="Лицензия" />
            </a>
          </div>
          <div className="col-4">
            <a href="/images/licenses/license_4.jpg">
              <Image src={license_4} alt="Лицензия" />
            </a>
          </div>
          <div className="col-4">
            <a href="/images/licenses/license_5.jpg">
              <Image src={license_5} alt="Лицензия" />
            </a>
          </div>
          <div className="col-4">
            <a href="/images/licenses/license_6.jpg">
              <Image src={license_6} alt="Лицензия" />
            </a>
          </div>
        </Row>

        <div className="contacts">
          <p className="mt-3">Контактная информация</p>
          <p className="ms-3">
            ООО «Социальная аптека»;
            <br />
            Адрес: Республика Дагестан, г. Махачкала, пр. Гамидова, дом 48;
            <br />
            Лицензия: № ЛО-05-02-001420 от 27 декабря 2019 г.;
            <br />
            ИНН 0571008484; ОГРН: 1160571061353
          </p>

          <p className="ms-3">
            Ответственное лицо за размещение информации о лекарственных
            препаратах: Газимагомедов Магомедарип Гасанович +7 (928) 98 444 68{" "}
            <a href="mailto:120x80@arip.info">120x80@arip.info</a>
          </p>
        </div>
      </Page>
    </Layout>
  );
};

export default About;
