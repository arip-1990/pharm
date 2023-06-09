import { FC, useCallback, useEffect } from "react";
import { GetServerSideProps } from "next";
import { useRouter } from "next/router";

import Layout from "../templates";
import Card from "../components/card";
import Pagination from "../components/pagination";
import { wrapper } from "../store";
import { useCookie } from "../hooks/useCookie";
import Breadcrumbs from "../components/breadcrumbs";
import { fetchDiscounts, useFetchDiscountsQuery, getRunningQueriesThunk } from "../lib/productService";

const Stock: FC = () => {
  const router = useRouter();
  const { page } = router.query;
  const [city] = useCookie("city");
  const { data, isLoading, isFetching, refetch } = useFetchDiscountsQuery(Number(page) || 1);

  useEffect(() => {
    const path = router.asPath.split("?")[0];
    if (Number(page) > 1) router.replace(path);
    else !isLoading && refetch();
  }, [city]);

  const getDefaultGenerator = useCallback(
    () => [{ href: "/stock", text: "Акции" }],
    []
  );

  return (
    <Layout loading={isFetching} title="Акции - Сеть аптек 120/80" description="Акции сайта.">
      <Breadcrumbs getDefaultGenerator={getDefaultGenerator} />

      <h5 className="text-center">{data?.title}</h5>
      <div className="row">
        {!isFetching && data?.data.length ? (
          <>
            <div
              className="row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-4 g-3 g-xl-4 mt-0"
              itemScope
              itemType="https://schema.org/ItemList"
            >
              <link itemProp="url" href="/catalog/stock" />
              {data?.data.map((product) => (
                <div key={product.id} className="col-10 offset-1 offset-sm-0">
                  <Card product={product} />
                </div>
              ))}
            </div>
            <div className="row mt-3">
              <div className="col">
                <Pagination
                  currentPage={data?.pagination?.current}
                  totalCount={data?.pagination?.total}
                  pageSize={data?.pagination?.pageSize}
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

    store.dispatch(fetchDiscounts.initiate(page));

    await Promise.all(store.dispatch(getRunningQueriesThunk()));

    return { props: {} };
  }
);

export default Stock;
