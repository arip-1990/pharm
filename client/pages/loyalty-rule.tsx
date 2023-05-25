import { FC, useCallback, useState } from "react";

import Layout from "../templates";
import Page from "../components/page";
import Breadcrumbs from "../components/breadcrumbs";
import Auth from "../components/auth";
import BaseLoyalty from "../components/loyalty";

const LoyaltyRule: FC = () => {
  const [showModal, setShowModal] = useState<boolean>(false);

  const getDefaultGenerator = useCallback(() => [
    { href: '/loyalty-rule', text: "Правила программы лояльности" }
  ], []);

  return (
    <Layout title="Правила программы лояльности - Сеть аптек 120/80" description="Мы заинтересованы в активном развитии нашей сети. Просим Вас внимательно ознакомиться с требованиями, предъявляемыми нами к потенциальным помещениям.">
      <Breadcrumbs getDefaultGenerator={getDefaultGenerator} />

      <Page title="Правила программы лояльности">
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

export default LoyaltyRule;
