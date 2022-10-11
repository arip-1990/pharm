import Layout from "../../components/layout";
import Card from "../../components/card";
import {FC, useCallback, useEffect} from "react";
import { GetServerSideProps } from "next";
import { ICategory } from "../../models/ICategory";
import Link from "next/link";
import Pagination from "../../components/pagination";
import { wrapper } from "../../store";
import {
  getRunningOperationPromises,
  searchProducts,
  useSearchProductsQuery,
} from "../../lib/catalogService";
import { useRouter } from "next/router";
import {
  fetchCategories,
  useFetchCategoriesQuery,
} from "../../lib/categoryService";
import { useCookie } from "../../hooks/useCookie";
import Breadcrumbs from "../../components/breadcrumbs";

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
  const [city] = useCookie("city");
  const router = useRouter();
  const { page, q } = router.query;
  const { data: products, refetch } = useSearchProductsQuery({
    q: String(q),
    page: Number(page) || 1,
  });
  const { data: categories } = useFetchCategoriesQuery();

    const getDefaultGenerator = useCallback(() => [
      {href: '/catalog', text: "Наш ассортимент"},
      {href: '/', text: `Поиск по запросу "${q}"`}
    ], [q]);

  useEffect(() => {
    const path = router.asPath.split("?")[0];
    if (Number(page) > 1) router.push(path);
    else refetch();
  }, [city]);

  return (
    <Layout>
        <Breadcrumbs getDefaultGenerator={getDefaultGenerator} />

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
                <link itemProp="url" href={`/catalog/search?q=${q}`} />
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
            <h3 className="text-center">По запросу "{q}" ничего не найдено!</h3>
          )}
        </div>
      </div>
    </Layout>
  );
};

export const getServerSideProps: GetServerSideProps = wrapper.getServerSideProps(
  (store) => async ({ params }) => {
    const page = Number(params?.page) || 1;
    const q = String(params?.q) || "";

    store.dispatch(searchProducts.initiate({ q, page }));
    store.dispatch(fetchCategories.initiate());

    await Promise.all(getRunningOperationPromises());

    return { props: {} };
  }
);

export default Search;
