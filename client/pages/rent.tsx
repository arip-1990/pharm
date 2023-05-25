import { FC, useCallback } from "react";

import Layout from "../templates";
import Page from "../components/page";
import Breadcrumbs from "../components/breadcrumbs";

const Rent: FC = () => {
  const getDefaultGenerator = useCallback(() => [
    { href: '/rent', text: "Развитие сети/Аренда" }
  ], []);

  return (
    <Layout title="Развитие сети/Аренда - Сеть аптек 120/80" description="Мы заинтересованы в активном развитии нашей сети. Просим Вас внимательно ознакомиться с требованиями, предъявляемыми нами к потенциальным помещениям.">
      <Breadcrumbs getDefaultGenerator={getDefaultGenerator} />

      <Page title="Развитие сети/Аренда">
        <p>
          Мы заинтересованы в активном развитии нашей сети. Просим Вас
          внимательно ознакомиться с требованиями, предъявляемыми нами к
          потенциальным помещениям.
        </p>
        <p>
          Если ваше предложение отвечает нашим требованиям, просим Вас прислать
          его по указанному адресу. После проведения экспертизы предложения и
          последующей заинтересованности, мы обязательно свяжемся с Вами.
        </p>
        <p>Регион расположения: Дагестан</p>
        <p>Право пользования: Собственность / Аренда / Субаренда</p>
        <p>Площадь: от 60 – 200 кв.м.</p>

        <p className="text-center fw-bold">Местонахождение:</p>
        <ul>
          <li>
            Обязательна высокая интенсивность потока потенциальных посетителей.
          </li>
          <li>Наличие вблизи оживленных транспортных магистралей.</li>
          <li>Наличие остановок общественного транспорта.</li>
          <li>Наличие автостоянки или возможности парковки.</li>
          <li>
            Наличие вблизи предприятий, крупных торговых центров и бытового
            обслуживания.
          </li>
          <li>Наличие вблизи лечебных учреждений.</li>
        </ul>

        <p className="text-center fw-bold">Состояние объекта:</p>
        <ul>
          <li>Наличие в помещении отдельного входа.</li>
          <li>Наличие водоснабжения и возможности устройства санузла.</li>
          <li>Наличие витринного пространства.</li>
          <li>
            Возможность размещения полноценной рекламной вывески и консоли
            «крест», согласно утвержденной концепции оформления фасада.
          </li>
        </ul>

        <h5 className="text-center fw-bold">Ждем Ваших предложений</h5>
        <p>
          Параметры предлагаемого помещения и условия можете отправить на почту{" "}
          <a href="mailto:call@apteka-05.ru">call@apteka-05.ru</a>
        </p>
      </Page>
    </Layout>
  );
};

export default Rent;
