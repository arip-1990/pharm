import { FC, useCallback, useEffect } from "react";
import { GetServerSideProps } from "next";
import { useRouter } from "next/router";

import Layout from "../templates";
import Card from "../components/card";
import Pagination from "../components/pagination";
import { wrapper } from "../store";
import { useCookie } from "../hooks/useCookie";
import Breadcrumbs from "../components/breadcrumbs";
import {
  fetchProducts,
  getRunningQueriesThunk,
  useFetchProductsQuery,
} from "../lib/productService";
import { useFetchCategoriesQuery } from "../lib/categoryService";
import { Category } from "../templates/category";

const Products: FC = () => {
  const router = useRouter();
  const { page } = router.query;
  const [city] = useCookie("city");
  const { data: products, isFetching, refetch } = useFetchProductsQuery({
    page: Number(page) || 1,
  });
  const { data: categories } = useFetchCategoriesQuery();

  useEffect(() => {
    const path = router.asPath.split("?")[0];
    if (Number(page) > 1) router.push(path);
    else refetch();
  }, [city]);

  const getDefaultGenerator = useCallback(
    () => [{ href: "/products", text: "Наш ассортимент" }],
    []
  );

  return (
    <Layout
      loading={isFetching}
      title="Наш ассортимент - Сеть аптек 120/80"
      description="Вы можете совершить покупку и забрать свой заказ самостоятельно, приехав в аптеку. Оплата при получении наличными или картой."
    >
      <Breadcrumbs getDefaultGenerator={getDefaultGenerator} />

      <div className="row">
        <nav className="col-md-3">
          <Category data={categories || []} />
        </nav>

        <div className="col-md-9 mt-3 mt-md-0">
          {products?.data.length ? (
            <>
              <div
                className="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-3 g-lg-4"
                itemScope
                itemType="https://schema.org/ItemList"
              >
                <link itemProp="url" href="/catalog" />
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
            <h3 className="text-center">Товары отсутствуют</h3>
          )}
        </div>
      </div>
    </Layout>
  );
};

export const getServerSideProps: GetServerSideProps = wrapper.getServerSideProps(
  (store) => async ({ params }) => {
    const page = Number(params?.page) || 1;

    store.dispatch(fetchProducts.initiate({ page }));

    await Promise.all(store.dispatch(getRunningQueriesThunk()));

    return { props: {} };
  }
);

export default Products;
