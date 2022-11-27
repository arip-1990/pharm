import Layout from "../components/layout";
import Page from "../components/page";
import { FC, useCallback, useState } from "react";
import Breadcrumbs from "../components/breadcrumbs";
import Auth from "../components/auth";
import BaseLoyalty from "../components/loyalty";

const Loyalty: FC = () => {
  const [showModal, setShowModal] = useState<boolean>(false);

  const getDefaultGenerator = useCallback(() => [
    { href: '/loyalty', text: "Программа лояльности" }
  ], []);

  return (
    <Layout title="Программа лояльности - Сеть аптек 120/80" description="Мы заинтересованы в активном развитии нашей сети. Просим Вас внимательно ознакомиться с требованиями, предъявляемыми нами к потенциальным помещениям.">
      <Breadcrumbs getDefaultGenerator={getDefaultGenerator} />

      <Page title="Программа лояльности">
        <BaseLoyalty />
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
