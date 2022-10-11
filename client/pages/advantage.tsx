import Layout from "../components/layout";
import Page from "../components/page";
import BaseAdvantage from "../components/advantage";

import advantage_1 from "../assets/images/advantage/1.jpg";
import advantage_2 from "../assets/images/advantage/2.jpg";
import advantage_3 from "../assets/images/advantage/3.jpg";
import advantage_4 from "../assets/images/advantage/4.jpg";
import { FC, useCallback } from "react";
import Head from "next/head";
import Breadcrumbs from "../components/breadcrumbs";

const Advantage: FC = () => {
  const getDefaultGenerator = useCallback(() => [
    { href: '/advantage', text: "Наши преимущества" }
  ], []);

  return (
    <Layout>
      <Head>
        <title>Сеть аптек 120/80 | Наши преимущества</title>
        <meta
          key="description"
          name="description"
          content="Создавая каждую аптеку, мы стремимся, чтобы в ней Вы нашли все необходимое. Компания выбирает партнеров-поставщиков согласно требованиям потребителей и обеспечивает постоянное наличие на складах огромного количества товаров."
        />
      </Head>

      <Breadcrumbs getDefaultGenerator={getDefaultGenerator} />

      <Page
        title="Преимущества наших аптек"
        style={{ backgroundColor: "transparent" }}
      >
        <BaseAdvantage title="Широкий ассортимент" image={advantage_1}>
          <p>
            Создавая каждую аптеку, мы стремимся, чтобы в ней Вы нашли все
            необходимое. Компания выбирает партнеров-поставщиков согласно
            требованиям потребителей и обеспечивает постоянное наличие на
            складах огромного количества товаров.
          </p>
          <p>
            Время в нашей жизни играет огромную роль и никому не хочется ездить
            по всему городу, пытаясь найти нужный препарат в нужной дозировке.
            Лучше проведите это время с семьей, а мы в этом поможем!
          </p>
          <p>Нет ничего проще, просто позвоните нам!</p>
        </BaseAdvantage>

        <BaseAdvantage title="Система" image={advantage_2}>
          <p>
            Все аптеки нашей сети являются единым целым и работают сообща,
            ежедневно обеспечивая сотни людей лекарственными средствами,
            витаминами, средствами красоты и товарами для малышей. Сплоченный
            коллектив работает как часы, поддерживая необходимый ассортимент в
            разных уголках нашего города. Новое направление наших аптек —
            доставка в каждый дом! Больше Вам не придется ломать голову с кем
            оставить малыша, чтобы сбегать в аптеку, не нужно будет просить
            родственников и соседей, нужно просто позвонить по телефону и
            получив консультацию специалиста, оформить заказ.
          </p>
          <p>
            Мы, в нашей деятельности, стремимся соответствовать высоким
            стандартам качества обслуживания, делая Вашу жизнь более комфортной,
            уютной и здоровой!
          </p>
        </BaseAdvantage>

        <BaseAdvantage title="Персонал" image={advantage_3}>
          <p>
            Повышение профессионализма сотрудников является стратегической
            задачей для сети аптек «120/80». Еженедельно представители
            фармкомпаний проводят обучение наших сотрудников, делясь своим
            опытом и открывая им новинки в мире лекарственных средств.
          </p>
          <p>
            Ежегодно в компании проводятся обучающие тренинги для работников. На
            них провизоры и фармацевты обучаются стандарту обслуживания,
            совершенствуют свое профессиональное мастерство и умение, получают
            новую информацию, что способствует дальнейшей качественной работе. В
            тренингах принимают активное участие и наши партнёры. В итоге, после
            прохождения обучающих программ фармацевты в наших аптеках готовы к
            профессиональному обслуживанию и консультациям.
          </p>
        </BaseAdvantage>

        <BaseAdvantage title="Сервис">
          <p>
            Качество обслуживания в медицине играет очень важную роль. Команда
            профессионалов, работающих в аптеках «120/80» окажет Вам
            квалифицированную помощь в выборе и поиске нужного товара. Мы знаем
            все о новинках фармацевтического рынка, о показаниях и
            противопоказаниях. Просто позвоните нам и Вы получите
            квалифицированную помощь, узнаете о наличии необходимого препарата,
            сможете зарезервировать или оформить заказ.
          </p>
          <p>
            Нет возможности приехать в аптеку, выход есть — наш специалист
            доставит необходимый товар до Вашей квартиры.
          </p>
          <p>
            Для Вашего удобства в аптеках действует наличный и безналичный
            способ оплаты.
          </p>
        </BaseAdvantage>

        <BaseAdvantage title="Поставщики" image={advantage_4}>
          <p>
            Система аптек «120/80» закупает товары напрямую у ведущих компаний,
            специализирующихся на производстве и дистрибуции товаров для красоты
            и здоровья. Именно поэтому мы даем нашим покупателям 100% гарантию
            качества всех наименований нашего ассортиментного перечня:
            лекарственных препаратов, косметики, БАД и товаров медицинского
            назначения.
          </p>
        </BaseAdvantage>
      </Page>
    </Layout>
  );
};

export default Advantage;
