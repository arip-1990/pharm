import Layout from "../components/layout";
import Page from "../components/page";
import { FC, useCallback, useState } from "react";
import Head from "next/head";
import Breadcrumbs from "../components/breadcrumbs";
import Auth from "../components/auth";
import BaseLoyalty from "../components/loyalty";

const Loyalty: FC = () => {
  const [showModal, setShowModal] = useState<boolean>(false);

  const getDefaultGenerator = useCallback(() => [
    { href: '/loyalty', text: "Программа лояльности" }
  ], []);

  return (
    <Layout>
      <Head>
        <title>Сеть аптек 120/80 | Программа лояльности</title>
        <meta
          key="description"
          name="description"
          content="Мы заинтересованы в активном развитии нашей сети. Просим Вас внимательно ознакомиться с требованиями, предъявляемыми нами к потенциальным помещениям."
        />
      </Head>

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
