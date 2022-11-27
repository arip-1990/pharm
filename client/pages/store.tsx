import { GetServerSideProps } from "next";
import { FC, useCallback, useEffect } from "react";
import Layout from "../components/layout";
import Page from "../components/page";
import Pagination from "../components/pagination";
import Link from "next/link";
import Breadcrumbs from "../components/breadcrumbs";
import { wrapper } from "../store";
import {
  fetchStores,
  getRunningOperationPromises,
  useFetchStoresQuery,
} from "../lib/storeService";
import { useRouter } from "next/router";
import Map from "../components/Map";
import { useCookie } from "../hooks/useCookie";

const Store: FC = () => {
  const router = useRouter();
  const { page } = router.query;
  const [city] = useCookie("city");
  const { data, isFetching, refetch } = useFetchStoresQuery(Number(page) || 1);

  useEffect(() => {
    const path = router.asPath.split("?")[0];
    if (Number(page) > 1) router.push(path);
    else refetch();
  }, [city]);

  const getDefaultGenerator = useCallback(() => [
    { href: '/store', text: "Точки самовывоза" }
  ], []);

  let points = [];

  return (
    <Layout title="Точки самовывоза - Сеть аптек 120/80" description="Точки самовывоза.">
      <Breadcrumbs getDefaultGenerator={getDefaultGenerator} />

      <Page title="Точки самовывоза">
        <Map points={points} />

        {data?.data.map((item) => {
          points.push({
            title: item.name,
            description: item.phone,
            coordinates: item.location.coordinate,
          });

          return (
            <div key={item.id} className="row address">
              <div className="col-12 col-md-5 text-center text-md-start">
                <span>{item.location.address}</span>
              </div>
              <div
                className="col-12 col-md-4 col-lg-3 text-center"
                dangerouslySetInnerHTML={{ __html: item.schedule }}
              />
              <div className="col-12 col-md-3 col-lg-2 text-center text-md-end">
                {item.phone}
              </div>
              <div className="col-12 col-lg-2 d-flex justify-content-end position-relative">
                <Link href={`/store/${item.slug}`}>
                  <a className="btn btn-sm btn-primary">Посмотреть</a>
                </Link>
              </div>
            </div>
          );
        })}

        <Pagination
          currentPage={data?.meta.current_page}
          totalCount={data?.meta.total}
          pageSize={data?.meta.per_page}
        />
      </Page>
    </Layout>
  );
};

export const getServerSideProps: GetServerSideProps = wrapper.getServerSideProps(
  (store) => async ({ params }) => {
    const page = Number(params?.page) || 1;

    store.dispatch(fetchStores.initiate(page));

    await Promise.all(getRunningOperationPromises());

    return { props: {} };
  }
);

export default Store;
