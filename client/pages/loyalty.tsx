import Layout from "../components/layout";
import Page from "../components/page";
import { FC, useCallback } from "react";
import Head from "next/head";
import Breadcrumbs from "../components/breadcrumbs";

const Loyalty: FC = () => {
  const getDefaultTextGenerator = useCallback((subpath: string) => {
    return (
      { loyalty: "Программа лояльности" }[subpath] ||
      subpath[0].toUpperCase() + subpath.substring(1).toLowerCase()
    );
  }, []);

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

      <Breadcrumbs getDefaultTextGenerator={getDefaultTextGenerator} />

      <Page title="Программа лояльности"></Page>
    </Layout>
  );
};

export default Loyalty;
