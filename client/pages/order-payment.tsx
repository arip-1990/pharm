import Layout from "../components/layout";
import Page from "../components/page";
import { FC, useCallback } from "react";
import Head from "next/head";
import Breadcrumbs from "../components/breadcrumbs";

const OrderPayment: FC = () => {
  const getDefaultTextGenerator = useCallback((subpath: string) => {
    return (
      { "order-payment": "Оплата заказа" }[subpath] ||
      subpath[0].toUpperCase() + subpath.substring(1).toLowerCase()
    );
  }, []);

  return (
    <Layout>
      <Head>
        <title>Сеть аптек 120/80 | Оплата заказа</title>
        <meta
          key="description"
          name="description"
          content="Способы оплаты при получении заказа в аптеке: Наличными; Оплата банковской картой."
        />
      </Head>

      <Breadcrumbs getDefaultTextGenerator={getDefaultTextGenerator} />

      <Page>
        <h6 className="text-center fw-bold">
          Способы оплаты при получении заказа в аптеке:
        </h6>
        <p>
          <span className="fw-bold">Наличными.</span> Оплата осуществляется
          непосредственно на кассе аптеки в момент выдачи заказа;
        </p>
        <p>
          <span className="fw-bold">Оплата банковской картой.</span> К оплате
          принимаются банковские карты платежных систем Visa, MasterCard,
          Maestro, МИР.
        </p>

        <h6 className="text-center fw-bold">
          Способы оплаты при оформлении доставки заказа:
        </h6>
        <p>
          <span className="fw-bold">Наличными.</span> Оплата заказа и доставки
          осуществляется непосредственно курьеру при получении заказа
        </p>
        <p>
          <span className="fw-bold">Оплата банковской картой на сайте.</span> К
          оплате принимаются банковские карты платежных систем Visa, MasterCard,
          Maestro, МИР.
        </p>
        <p>Предоставляются платежные документы - кассовый и товарные чеки.</p>

        <p>
          В случае возникновении проблем с оплатой, Вы можете обратиться за
          поддержкой по номеру{" "}
          <span className="fw-bold">+7 (928) 984-44-68</span>,
        </p>
        <p>
          либо обратиться в единую справочную сети{" "}
          <span className="fw-bold">+7 (8722) 606-366</span>
        </p>

        <h6 className="text-center fw-bold">
          Описание процесса передачи данных
        </h6>
        <div style={{ fontSize: "0.75rem" }}>
          <p>
            Для оплаты покупки Вы будете перенаправлены на платежный шлюз ПАО
            СБЕРБАНК для ввода реквизитов Вашей карты. Пожалуйста, приготовьте
            Вашу пластиковую карту заранее. Соединение с платежным шлюзом, и
            передача информации осуществляется в защищенном режиме с
            использованием протокола шифрования SSL.
          </p>
          <p>
            В случае если Ваш банк поддерживает технологию безопасного
            проведения интернет платежей Verified By Visa или MasterCard Secure
            Code для проведения платежа также может потребоваться ввод
            специального пароля. Способы и возможность получения паролей для
            совершения интернет-платежей Вы можете уточнить в банке, выпустившем
            карту.
          </p>
          <p>
            Настоящий сайт поддерживает 256-битное шифрование.
            Конфиденциальность сообщаемой персональной информации обеспечивается
            ПАО СБЕРБАНК. Введенная информация не будет предоставлена третьим
            лицам за исключением случаев, предусмотренных законодательством РФ.
            Проведение платежей по банковским картам осуществляется в строгом
            соответствии с требованиями платежных систем Visa Int. и MasterCard
            Europe Sprl.
          </p>
        </div>
      </Page>
    </Layout>
  );
};

export default OrderPayment;