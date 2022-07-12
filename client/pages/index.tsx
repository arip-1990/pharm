import Layout from "../components/layout";
import Card from "../components/card";
import React, { FC } from "react";
import { IProduct } from "../models/IProduct";
import { GetServerSideProps } from "next";
import { getPopularProducts } from "../lib/catalog";
import { wrapper } from "../services/store";
import { setProductData } from "../services/productService";

type Props = {
  products: IProduct[];
};

const Home: FC<Props> = ({ products }) => {
  return (
    <Layout banner>
      <div
        className="row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-4 g-3 g-xl-4"
        itemScope
        itemType="https://schema.org/ItemList"
      >
        <link itemProp="url" href="/" />
        {products.map((product) => (
          <div key={product.id} className="col-10 offset-1 offset-sm-0">
            <Card product={product} />
          </div>
        ))}
      </div>
    </Layout>
  );
};

export const getServerSideProps: GetServerSideProps<Props> = wrapper.getServerSideProps(
  (store) => async (ctx) => {
    try {
      const data = await getPopularProducts();

      store.dispatch(setProductData(data));

      return {
        props: {
          products: data,
        },
      };
    } catch (error) {
      console.log(error);

      return {
        props: { products: [] },
      };
    }
  }
);

export default Home;
