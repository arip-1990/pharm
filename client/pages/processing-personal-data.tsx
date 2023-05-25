import { FC, useCallback } from "react";

import Layout from "../templates";
import Page from "../components/page";
import Breadcrumbs from "../components/breadcrumbs";

const ProcessingPersonalData: FC = () => {
  const getDefaultGenerator = useCallback(() => [
    { href: '/processing-personal-data', text: "Обработка персональных данных" }
  ], []);

  return (
    <Layout title="Обработка персональных данных - Сеть аптек 120/80" description="Покупатель, предоставляя свои персональные данные даёт согласие на обработку, хранение и использование своих персональных данных на основании ФЗ №152-ФЗ «О персональных данных» от 27.07.2006г.">
      <Breadcrumbs getDefaultGenerator={getDefaultGenerator} />

      <Page title="Обработка персональных данных">
        <p>
          Покупатель, предоставляя свои персональные данные даёт согласие на
          обработку, хранение и использование своих персональных данных на
          основании ФЗ №152-ФЗ «О персональных данных» от 27.07.2006г. в
          следующих целях:
        </p>
        <p>Регистрации на сайте.</p>
        <p>Реализация клиентской поддержки.</p>
        <p>Информирования клиента о маркетинговых акциях.</p>
        <p>Выполнение Продавцом обязательств перед Покупателем.</p>
        <p>
          Персональными данными является любая информация личного характера,
          которая может установить личность Клиента:
        </p>
        <ul className="ms-4">
          <li>ФИО</li>
          <li>Дата рождения</li>
          <li>Номер телефона</li>
          <li>Адрес электронной почты</li>
          <li>Почтовый адрес</li>
        </ul>

        <p>
          Персональные данные Клиента хранятся только на электронных носителях и
          обрабатываются с использованием автоматизированных систем.
        </p>
        <p>
          Продавец обязуется не передавать персональные данные третьим лицам, за
          исключением следующих случаев:
        </p>
        <ul className="ms-4">
          <li>
            По запросам уполномоченных органов государственной власти РФ только
            по основаниям и в порядке, установленным законодательством РФ
          </li>
          <li>
            Стратегическим партнерам, которые сотрудничают с Продавцом для
            предоставления продуктов и услуг, которые помогают Продавцу
            реализовывать товары и услуги потребителям.
          </li>
        </ul>

        <p>
          Продавец оставляет за собой право вносить изменения в одностороннем
          порядке в настоящие правила, при условии, что изменения не
          противоречат действующему законодательству РФ. Изменения условий
          настоящих правил вступают в силу после их публикации на Сайте.
        </p>
      </Page>
    </Layout>
  );
};

export default ProcessingPersonalData;
