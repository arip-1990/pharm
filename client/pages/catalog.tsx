import Layout from "../components/layout";
import Card from "../components/card";
import { FC, useState } from "react";
import { GetServerSideProps } from "next";
import { ICategory } from "../models/ICategory";
import saleImage from "../assets/images/sale-icon.png";
import Image from "next/image";
import Link from "next/link";
import Paginate from "../components/Paginate";
import { wrapper } from "../lib/store";
import {
  fetchCategories,
  getRunningOperationPromises,
  useFetchCategoriesQuery,
} from "../lib/categoryService";
import { useFetchProductsQuery } from "../lib/productService";

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
  const [pagination, setPagination] = useState<number>(1);
  const { data: categories } = useFetchCategoriesQuery();
  const { data: products } = useFetchProductsQuery({ page: pagination });

  return (
    <Layout>
      <div className="alert alert-danger" role="alert">
        В связи с повышенным спросом мы вынуждены ввести временное ограничение
        продажи лекарственных средств «НЕ БОЛЕЕ 2 УП В ОДНИ РУКИ»
      </div>

      <div className="row">
        <nav className="col-md-3">
          <ul className="category">
            <li className="sale">
              <a href="/catalog/sale">
                <Image width={36} height={36} src={saleImage} alt="" />
                Распродажа
              </a>
            </li>
            {categories?.map((item) => generateCategory(item))}
          </ul>
        </nav>

        <div className="col-md-9 mt-3 mt-md-0">
          {products?.data.length ? (
            <>
              <div
                className="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-3 g-lg-4"
                itemScope
                itemType="https://schema.org/ItemList"
              >
                <link itemProp="url" href="/products" />
                {products?.data.map((product) => (
                  <div key={product.id} className="col-10 offset-1 offset-sm-0">
                    <Card product={product} />
                  </div>
                ))}
              </div>
              <div className="row mt-3">
                <div className="col">
                  <Paginate
                    current={products?.meta.current_page}
                    total={products?.meta.total}
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
  (store) => async (context) => {
    store.dispatch(fetchCategories.initiate());

    await Promise.all(getRunningOperationPromises());

    return { props: {} };
  }
);

export default Catalog;
