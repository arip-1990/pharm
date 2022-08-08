import Layout from "../components/layout";
import Card from "../components/card";
import { FC } from "react";
import { GetServerSideProps } from "next";
import { ICategory } from "../models/ICategory";
import saleImage from "../assets/images/sale-icon.png";
import Image from "next/image";
import Link from "next/link";
import Paginate from "../components/Paginate";
import { wrapper } from "../lib/store";
import {
  fetchProducts,
  getRunningOperationPromises,
  useFetchProductsQuery,
} from "../lib/catalogService";
import { useRouter } from "next/router";
import api from "../lib/api";

const generateCategory = (category: ICategory) => {
  return (
    <li key={category.id}>
      <Link href={`/catalog/${category.slug}`}>
        <a>
          {category.parent ? null : (
            <Image
              width={36}
              height={36}
              src={`/assets/images/category/cat_${category.id}.png`}
              alt=""
            />
          )}
          {category.name}
        </a>
      </Link>
      {category.children.length ? (
        <div className="overlay">
          <ul>
            {category.children
              .filter((_, i) => i < 10)
              .map((item) => generateCategory(item))}
            {category.children.length > 10 ? (
              <li>
                <Link href={`/catalog/${category.slug}`}>
                  <a>{category.name}</a>
                </Link>
              </li>
            ) : null}
          </ul>
        </div>
      ) : null}
    </li>
  );
};

const Catalog: FC = () => {
  const router = useRouter();
  const { page } = router.query;
  const { data } = useFetchProductsQuery({ page: Number(page) || 1 });

  return (
    <Layout>
      <div className="row">
        <nav className="col-md-3">
          <ul className="category">
            <li className="sale">
              <a href="/catalog/sale">
                <Image width={36} height={36} src={saleImage} alt="" />
                Распродажа
              </a>
            </li>
            {data?.categories.map((item) => generateCategory(item))}
          </ul>
        </nav>

        <div className="col-md-9 mt-3 mt-md-0">
          {data?.products.length ? (
            <>
              <div
                className="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-3 g-lg-4"
                itemScope
                itemType="https://schema.org/ItemList"
              >
                <link itemProp="url" href="/products" />
                {data?.products.map((product) => (
                  <div key={product.id} className="col-10 offset-1 offset-sm-0">
                    <Card product={product} />
                  </div>
                ))}
              </div>
              <div className="row mt-3">
                <div className="col">
                  <Paginate
                    current={data?.meta.current_page}
                    total={data?.meta.total}
                  />
                </div>
              </div>
            </>
          ) : (
            <h3 className="text-center">Товары отсутствуют</h3>
          )}
        </div>
      </div>
    </Layout>
  );
};

export const getServerSideProps: GetServerSideProps = wrapper.getServerSideProps(
  (store) => async ({ req, params }) => {
    if (req) api.defaults.headers.get.Cookie = req.headers.cookie;
    const page = params?.page || 1;

    store.dispatch(fetchProducts.initiate({ page: Number(page) }));

    await Promise.all(getRunningOperationPromises());

    return { props: {} };
  }
);

export default Catalog;
