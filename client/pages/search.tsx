import Layout from "../components/layout";
import Card from "../components/card";
import { FC } from "react";
import { GetServerSideProps } from "next";
import { ICategory } from "../models/ICategory";
import saleImage from "../assets/images/sale-icon.png";
import Link from "next/link";
import Pagination from "../components/pagination";
import { wrapper } from "../lib/store";
import {
  getRunningOperationPromises,
  searchProducts,
  useSearchProductsQuery,
} from "../lib/catalogService";
import { useRouter } from "next/router";
import api from "../lib/api";
import {
  fetchCategories,
  useFetchCategoriesQuery,
} from "../lib/categoryService";

const generateCategory = (category: ICategory) => {
  return (
    <li key={category.id}>
      <Link href={`/catalog/${category.slug}`}>
        <a>
          {category.picture && <img src={category.picture} alt="" />}
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

const Search: FC = () => {
  const router = useRouter();
  const { page, q } = router.query;
  const { data: products } = useSearchProductsQuery({
    q: String(q),
    page: Number(page) || 1,
  });
  const { data: categories } = useFetchCategoriesQuery();

  return (
    <Layout>
      <div className="row">
        <nav className="col-md-3">
          <ul className="category">
            {/* <li className="sale">
              <a href="/catalog/sale">
                <Image width={36} height={36} src={saleImage} alt="" />
                Распродажа
              </a>
            </li> */}
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
                  <Pagination
                    currentPage={products?.meta.current_page}
                    totalCount={products?.meta.total}
                    pageSize={products?.meta.per_page}
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
    const page = Number(params?.page) || 1;
    const q = String(params?.q) || "";

    store.dispatch(searchProducts.initiate({ q, page }));
    store.dispatch(fetchCategories.initiate());

    await Promise.all(getRunningOperationPromises());

    return { props: {} };
  }
);

export default Search;
