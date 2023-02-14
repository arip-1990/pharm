import Link from "next/link";
import Layout from "../templates";
import Page from "../components/page";
import { FC, useCallback, useState } from "react";
import Breadcrumbs from "../components/breadcrumbs";
import Auth from "../components/auth";

const Loyalty: FC = () => {
  const [showModal, setShowModal] = useState<boolean>(false);

  const getDefaultGenerator = useCallback(() => [
    { href: '/loyalty', text: "Программа лояльности" }
  ], []);

  return (
    <Layout title="Программа лояльности - Сеть аптек 120/80" description="Мы заинтересованы в активном развитии нашей сети. Просим Вас внимательно ознакомиться с требованиями, предъявляемыми нами к потенциальным помещениям.">
      <Breadcrumbs getDefaultGenerator={getDefaultGenerator} />

      <Page title="Программа лояльности">
        <span>Бонусная карта сети аптек «<b>120/80</b>» выдаётся при разовой покупке свыше <b>500 рублей</b>.</span><br /><br />
        <span>Бонусы начисляются с каждой покупки в размере <b>1</b>% от суммы покупки, и до <b>50</b>% на товары, участвующие в Акции;</span>
        <ul>
          <br /><li>Баллы начисляются на весь ассортимент; списываются на весь ассортимент кроме интернет-заказов;</li><br />
          <li>Баллы становятся активными и доступными к списанию на следующий день после оплаты покупки;</li><br />
          <li>Баллы списываются по формуле: <b>10 баллов = 1 рубль;</b></li><br />
          <li>Оплата товаров баллами возможна до <b>99</b>% от суммы чека, минимальная сумма для списания не ограничена;</li><br />
          <li>На товары участвующие в акциях действует <b>повышенный процент накопления бонусов</b>*</li>
        </ul>
        <small>*Акционные позиции уточняйте у фармацевтов, либо на нашем сайте <Link href="/"><a><b>https://120на80.рф</b></a></Link> в разделе «<b>АКЦИИ</b>»</small>
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
