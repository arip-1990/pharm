import Layout from "../components/layout";
import Card from "../components/card";
import React, { FC, useEffect, useState } from "react";
import { GetServerSideProps } from "next";
import { wrapper } from "../lib/store";
import {
  fetchPopularProducts,
  useFetchPopularProductsQuery,
  getRunningOperationPromises,
} from "../lib/catalogService";
import Head from "next/head";
import { useRouter } from "next/router";
import Auth from "../components/auth";
import { useCookie } from "../hooks/useCookie";

type AuthType = "login" | "register";

const Home: FC = () => {
  const [authModal, setAuthModal] = useState<{
    type: AuthType;
    show: boolean;
  }>({ type: "login", show: false });
  const [city] = useCookie("city");
  const { data, isFetching, refetch } = useFetchPopularProductsQuery();
  const router = useRouter();

  useEffect(() => {
    const path = router.asPath.split("#");
    if (path.length > 1 && ["login", "register"].includes(path[1])) {
      const hash = path[1] as AuthType;
      setAuthModal({ type: hash, show: true });
    }
  }, []);

  useEffect(() => refetch(), [city]);

  return (
    <Layout banner>
      <Head>
        <title>Сеть аптек 120/80</title>
        <meta
          key="description"
          name="description"
          content="Добро пожаловать на наш сайт - сервис для покупки лекарств и товаров в собственной аптечной сети! Наши аптеки популярны, благодаря широкому ассортименту и высокой культуре обслуживания при доступных ценах. Гарантия качества и сервисное обслуживание – основные принципы нашей работы!"
        />
      </Head>

      <div
        className="row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-4 g-3 g-xl-4"
        itemScope
        itemType="https://schema.org/ItemList"
      >
        <link itemProp="url" href="/" />
        {data?.map((product) => (
          <div key={product.id} className="col-10 offset-1 offset-sm-0">
            <Card product={product} />
          </div>
        ))}
      </div>

      <Auth
        show={authModal.show}
        type={authModal.type}
        onHide={() => setAuthModal((item) => ({ ...item, show: false }))}
      />
    </Layout>
  );
};

export const getServerSideProps: GetServerSideProps = wrapper.getServerSideProps(
  (store) => async () => {
    store.dispatch(fetchPopularProducts.initiate());

    await Promise.all(getRunningOperationPromises());

    return { props: {} };
  }
);

export default Home;
