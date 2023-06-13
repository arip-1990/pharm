import { FC, useCallback, useEffect } from "react";
import { GetServerSideProps } from "next";
import { useRouter } from "next/router";

import Layout from "../../templates";
import Card from "../../components/card";
import Pagination from "../../components/pagination";
import { wrapper } from "../../store";
import {
  getRunningQueriesThunk,
  searchProducts,
  useSearchProductsQuery,
} from "../../lib/productService";
import {
  fetchCategories,
  useFetchCategoriesQuery,
} from "../../lib/categoryService";
import { useCookie } from "../../hooks/useCookie";
import Breadcrumbs from "../../components/breadcrumbs";
import { Category } from "../../templates/category";

const Search: FC = () => {
  const [city] = useCookie("city");
  const router = useRouter();
  const { page, q } = router.query;
  const { data: products, isFetching, refetch } = useSearchProductsQuery({
    q: q ? String(q) : "",
    page: Number(page) || 1,
    pageSize: 15,
  });
  const { data: categories } = useFetchCategoriesQuery();

  const getDefaultGenerator = useCallback(
    () => [
      { href: "/catalog", text: "Наш ассортимент" },
      { href: "/", text: `Поиск по запросу "${q ?? ""}"` },
    ],
    [q]
  );

  useEffect(() => {
    if (products) {
      if (page) router.replace(router.asPath.replace(/[?&]page=\d+/i, ""));
      else refetch();
    }
  }, [city]);

  const generateData = () => {
    if (isFetching)
      return (
        <div
          style={{
            display: "flex",
            justifyContent: "center",
            alignItems: "center",
          }}
        >
          <div className="spinner-border m-5 text-secondary" role="status">
            <span className="visually-hidden">Загрузка...</span>
          </div>
        </div>
      );

    return products?.data.length ? (
      <>
        <div
          className="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-3 g-lg-4"
          itemScope
          itemType="https://schema.org/ItemList"
        >
          <link itemProp="url" href={`/products/search?q=${q ?? ""}`} />
          {products?.data.map((product) => (
            <div key={product.id} className="col-10 offset-1 offset-sm-0">
              <Card product={product} />
            </div>
          ))}
        </div>
        <div className="row mt-3">
          <div className="col">
            <Pagination
              currentPage={products?.pagination?.current}
              totalCount={products?.pagination?.total}
              pageSize={products?.pagination?.pageSize}
            />
          </div>
        </div>
      </>
    ) : (
      <h5 style={{ textAlign: "center" }}>
        По запросу "{q ?? ""}" ничего не найдено!
      </h5>
    );
  };

  return (
    <Layout loading={isFetching} title="Поиск товара - Сеть аптек 120/80">
      <Breadcrumbs getDefaultGenerator={getDefaultGenerator} />

      <div className="row">
        <nav className="col-md-3">
          <Category data={categories || []} />
        </nav>

        <div className="col-md-9 mt-3 mt-md-0">{generateData()}</div>
      </div>
    </Layout>
  );
};

export const getServerSideProps: GetServerSideProps = wrapper.getServerSideProps(
  (store) => async ({ params }) => {
    const page = Number(params?.page) || 1;
    const pageSize = Number(params?.pageSize) || 15;
    const q = params?.q ? String(params?.q) : "";

    store.dispatch(searchProducts.initiate({ q, page, pageSize }));
    store.dispatch(fetchCategories.initiate());

    await Promise.all(store.dispatch(getRunningQueriesThunk()));

    return { props: {} };
  }
);

export default Search;
