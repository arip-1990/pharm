import Layout from "../components/layout";
import Card from "../components/card";
import React, { FC, useEffect } from "react";
import { GetServerSideProps } from "next";
import { wrapper } from "../lib/store";
import {
  fetchPopularProducts,
  useFetchPopularProductsQuery,
  getRunningOperationPromises,
} from "../lib/catalogService";
import Head from "next/head";
import { useCookies } from "react-cookie";
import axios from "axios";

const Home: FC = () => {
  const [{ city }] = useCookies(["city"]);
  const { data, isFetching, refetch } = useFetchPopularProductsQuery();

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
    </Layout>
  );
};

export const getServerSideProps: GetServerSideProps = wrapper.getServerSideProps(
  (store) => async ({ req }) => {
    if (req) axios.defaults.headers.common.Cookie = req.headers.cookie;

    store.dispatch(fetchPopularProducts.initiate());

    await Promise.all(getRunningOperationPromises());

    return { props: {} };
  }
);

export default Home;
