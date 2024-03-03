import { FC, useCallback } from "react";

import Layout from "../templates";
import Page from "../components/page";
import Breadcrumbs from "../components/breadcrumbs";

const Return: FC = () => {
  const getDefaultGenerator = useCallback(() => [
    { href: '/delete-data', text: "Порядок удаления данных из приложения 120на80.рф" }
  ], []);

  return (
    <Layout title="Порядок удаления данных из приложения 120на80.рф - Сеть аптек 120/80" description="Клиент в праве на обмен или возврат раннее заказанного товара в случаях...">
      <Breadcrumbs getDefaultGenerator={getDefaultGenerator} />

      <Page title="Порядок удаления данных из приложения 120на80.рф">
        <ol>
          <li>Позвоните по номеру +7 (8722) 606-366 или напишите письмо на почту info@120на80.рф</li>
          <li>При обращении укажите свой номер телефона, имя и сообщите о том, что Вы хотели бы удалить свои данные из мобильного приложения 120на80.рф</li>
          <li>Наши специалисты примут запрос, подтвердят что это действительно Вы (через телефон или email) и запустят процесс удаления данных.</li>
          <li>По окончании процесса удаления данных Вы будете оповещены по номеру телефона или email</li>
        </ol>
        <p>По окончании процесса удаления данных Вы будете оповещены по номеру телефона или email</p>
      </Page>
    </Layout>
  );
};

export default Return;
