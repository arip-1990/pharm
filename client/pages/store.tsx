import { GetServerSideProps } from "next";
import { FC, useCallback } from "react";
import Layout from "../components/layout";
import Page from "../components/page";
import Pagination from "../components/pagination";
import Link from "next/link";
import Head from "next/head";
import Breadcrumbs from "../components/breadcrumbs";
import { wrapper } from "../lib/store";
import {
  fetchStores,
  getRunningOperationPromises,
  useFetchStoresQuery,
} from "../lib/storeService";
import { useRouter } from "next/router";
import api from "../lib/api";
import Map from "../components/Map";

const Store: FC = () => {
  const router = useRouter();
  const { page } = router.query;
  const { data, isFetching } = useFetchStoresQuery(Number(page) || 1);

  const getDefaultTextGenerator = useCallback((subpath: string) => {
    return (
      { store: "Точки самовывоза" }[subpath] ||
      subpath[0].toUpperCase() + subpath.substring(1).toLowerCase()
    );
  }, []);

  let points = [];

  return (
    <Layout>
      <Head>
        <title>Сеть аптек 120/80 | Точки самовывоза</title>
        <meta key="description" name="description" content="Точки самовывоза" />
      </Head>

      <Breadcrumbs getDefaultTextGenerator={getDefaultTextGenerator} />

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
                <span>{item.name}</span>
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
  (store) => async ({ req, params }) => {
    if (req) api.defaults.headers.get.Cookie = req.headers.cookie;
    const page = Number(params?.page) || 1;

    store.dispatch(fetchStores.initiate(page));

    await Promise.all(getRunningOperationPromises());

    return { props: {} };
  }
);

export default Store;
