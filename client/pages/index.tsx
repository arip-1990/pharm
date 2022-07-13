import Layout from "../components/layout";
import Card from "../components/card";
import React, { FC } from "react";
import { GetServerSideProps } from "next";
import { wrapper } from "../lib/store";
import {
  fetchPopularProducts,
  useFetchPopularProductsQuery,
  getRunningOperationPromises,
} from "../lib/productService";

const Home: FC = () => {
  const { data, isLoading } = useFetchPopularProductsQuery();

  console.log(isLoading);

  return (
    <Layout banner>
      <div
        className="row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-4 g-3 g-xl-4"
        itemScope
        itemType="https://schema.org/ItemList"
      >
        <link itemProp="url" href="/" />
        {isLoading ? (
          <span>loading</span>
        ) : (
          data?.map((product) => (
            <div key={product.id} className="col-10 offset-1 offset-sm-0">
              <Card product={product} />
            </div>
          ))
        )}
      </div>
    </Layout>
  );
};

export const getServerSideProps: GetServerSideProps = wrapper.getServerSideProps(
  (store) => async (context) => {
    store.dispatch(fetchPopularProducts.initiate());

    await Promise.all(getRunningOperationPromises());

    return { props: {} };
  }
);

export default Home;
