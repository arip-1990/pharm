import Layout from "../components/layout";
import Page from "../components/page";
import { FC, MouseEvent, useCallback, useState } from "react";
import Head from "next/head";
import Breadcrumbs from "../components/breadcrumbs";
import Auth from "../components/auth";

const Banner: FC<{ handleClick: (e: MouseEvent) => void }> = ({
  handleClick,
}) => {
  return (
    <div className="loyalty-banner">
      <button className="button" onClick={handleClick}>
        Заполнить форму
      </button>
    </div>
  );
};

const Loyalty: FC = () => {
  const [showModal, setShowModal] = useState<boolean>(false);

  const getDefaultTextGenerator = useCallback((subpath: string) => {
    return (
      { loyalty: "Программа лояльности" }[subpath] ||
      subpath[0].toUpperCase() + subpath.substring(1).toLowerCase()
    );
  }, []);

  const handleClick = (e: MouseEvent) => {
    e.preventDefault();
    setShowModal(true);
  };

  return (
    <Layout banner={<Banner handleClick={handleClick} />}>
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

      <Auth
        show={showModal}
        type="register"
        onHide={() => setShowModal(false)}
      />
    </Layout>
  );
};

export default Loyalty;
