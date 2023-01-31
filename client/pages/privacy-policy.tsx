import Layout from "../templates";
import Page from "../components/page";
import { FC, useCallback } from "react";
import Breadcrumbs from "../components/breadcrumbs";

const PrivacyPolicy: FC = () => {
  const getDefaultGenerator = useCallback(() => [
    { href: '/privacy-policy', text: "Политика конфиденциальности" }
  ], []);

  return (
    <Layout title="Политика конфиденциальности - Сеть аптек 120/80" description="В соответствии с Федеральным законом № 152-ФЗ «О персональных данных» от 27.07.2006 года Вы подтверждаете свое согласие на обработку персональных данных: сбор, накопление, хранение, использование, блокирование, а также передачу информации третьим лицам.">
      <Breadcrumbs getDefaultGenerator={getDefaultGenerator} />

      <Page title="Политика конфиденциальности">
        <p>
          В соответствии с Федеральным законом № 152-ФЗ «О персональных данных»
          от 27.07.2006 года Вы подтверждаете свое согласие на обработку
          персональных данных: сбор, накопление, хранение, использование,
          блокирование, а также передачу информации третьим лицам.
        </p>
        <p>
          Интернет-аптека гарантирует 100% сохранность данных Клиента. Обработка
          персональных данных Клиента осуществляется в целях эффективного
          исполнения заказов и оказания услуг.
        </p>
        <p>
          Для регистрации и совершения покупки в интернет-аптеке, Клиенту
          необходимо предоставить персональные данные, являющиеся обязательными
          для оформления заказа.
        </p>
        <p>
          Персональными данными - это любые сведения, относящиеся к конкретному
          человеку и дающие возможность идентифицировать личность Клиента.
          Исключение составляет лишь информация, распространение которой
          допускается законодательством РФ.
        </p>

        <p>Принимая условия, Клиент должен:</p>
        <p>
          Оставить хотя бы минимальные данные о себе. При регистрации клиенту
          необходимо предоставить: ФИО, телефон, электронную почту, адрес
          доставки.
        </p>

        <p className="fw-bold">
          На какие данные распространяется политика конфиденциальности
        </p>
        <p>
          Политика конфиденциальности для сайта является любая персональная
          информация, требующаяся для обеспечения предоставления услуг Клиенту,
          которую тот сообщает в процессе регистрации или же при оформлении
          заказа. К таким данным, прежде всего, относятся: имя пользователя, его
          электронный адрес, контактный телефон, место жительства, а также адрес
          доставки товара
        </p>
        <p>
          В ходе обработки Продавец имеем право проводить с персональными
          данными следующие действия: записывать, систематизировать,
          накапливать, хранить, уточнять, извлекать, использовать, передавать
          для того, чтобы изучать потребности клиентов и повышать качество своих
          продуктов и услуг, обезличивать, блокировать, удалять, уничтожать.
        </p>

        <p className="fw-bold">Для чего требуется персональная информация?</p>
        <p>
          Обработка персональных данных осуществляется в целях исполнения
          заказов и иных услуг. Найти нужного Клиента, оформления заказа,
          обработки платежа, доставка товара;
        </p>
        <p>Предоставления доступа к персонализированной информации на сайте;</p>
        <p>
          Необходимо обратная связь с целью обработки заявок, уведомления о
          состоянии заказа, оказания помощи в возникших вопросах, связанных с
          пользованием услугами интернет-аптеки, предоставления информации о
          ценах, акциях, специальных предложениях, обновлении продукции;
        </p>
        <p>Периодического направления Вам информации о новостях и акциях.</p>
        <p>
          Если Клиент хочет отказаться от рассылки новой и актуальной
          информации, то Продавец должен удовлетворить данный запрос Клиента.
        </p>

        <p className="fw-bold">
          Кому может быть передана персональная информация?
        </p>
        <p>
          Продавец должен обеспечивает сохранность персональных данных и
          принимает все возможные меры,{" "}
          <span style={{ color: "#333" }}>
            исключающие доступ неуполномоченных лиц
          </span>{" "}
          и не передает информацию третьим лицам. За исключением передачи
          службам курьерской доставки, почтовым компаниям для организации
          доставки товара.
        </p>

        <p className="fw-bold">
          Когда может раскрываться персональная информация?
        </p>
        <p>
          Передача персональных данных третьим лицам осуществляется на основании
          законодательства Российской Федерации. Продавец сохраняет за собой
          право раскрыть персональные данные уполномоченным органам власти в
          предусмотренных законодательством РФ случаях или же на основании
          решения суда.
        </p>
      </Page>
    </Layout>
  );
};

export default PrivacyPolicy;
