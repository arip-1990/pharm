import {FC, useCallback, useEffect} from "react";
import { GetServerSideProps } from "next";
import { useRouter } from "next/router";
import Layout from "../components/layout";
import Card from "../components/card";
import Pagination from "../components/pagination";
import { wrapper } from "../store";
import {
  fetchStockProducts,
  getRunningOperationPromises,
  useFetchStockProductsQuery,
} from "../lib/catalogService";
import { useCookie } from "../hooks/useCookie";
import Breadcrumbs from "../components/breadcrumbs";

const Stock: FC = () => {
  const router = useRouter();
  const { page } = router.query;
  const [city] = useCookie("city");
  const { data, isFetching, refetch } = useFetchStockProductsQuery({
    page: Number(page) || 1,
  });

  useEffect(() => {
    const path = router.asPath.split("?")[0];
    if (Number(page) > 1) router.push(path);
    else refetch();
  }, [city]);

    const getDefaultGenerator = useCallback(() => [{ href: '/stock', text: "Акции" }], []);

  return (
    <Layout title="Акции - Сеть аптек 120/80" description="Акции сайта.">
      <Breadcrumbs getDefaultGenerator={getDefaultGenerator} />

      <h3 className="text-center">Период действия акции с 1 по 28 февраля 2023г.</h3>
      <div className="row">
        {data?.products.length ? (
          <>
            <div
              className="row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-4 g-3 g-xl-4 mt-0"
              itemScope
              itemType="https://schema.org/ItemList"
            >
              <link itemProp="url" href="/catalog/stock" />
              {data?.products.map((product) => (
                <div key={product.id} className="col-10 offset-1 offset-sm-0">
                  <Card product={product} />
                </div>
              ))}
            </div>
            <div className="row mt-3">
              <div className="col">
                <Pagination
                  currentPage={data?.meta.current_page}
                  totalCount={data?.meta.total}
                  pageSize={data?.meta.per_page}
                />
              </div>
            </div>
          </>
          ) : (
            <h3 className="text-center">Товары отсутствуют</h3>
          )}
      </div>
    </Layout>
  );
};

export const getServerSideProps: GetServerSideProps = wrapper.getServerSideProps(
  (store) => async ({ params }) => {
    const page = Number(params?.page) || 1;

    store.dispatch(fetchStockProducts.initiate({ page }));

    await Promise.all(getRunningOperationPromises());

    return { props: {} };
  }
);

export default Stock;
