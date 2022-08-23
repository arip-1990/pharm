import { GetServerSideProps } from "next";
import { Map, YMaps, Clusterer, Placemark } from "@pbe/react-yandex-maps";
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

  return (
    <Layout>
      <Head>
        <title>Сеть аптек 120/80 | Точки самовывоза</title>
        <meta key="description" name="description" content="Точки самовывоза" />
      </Head>

      <Breadcrumbs getDefaultTextGenerator={getDefaultTextGenerator} />

      <Page title="Точки самовывоза">
        <YMaps query={{ apikey: "de8de84b-e8b4-46c9-ba10-4cf2911deebf" }}>
          <Map
            width="100%"
            height={400}
            defaultState={{
              center: [42.961079, 47.534646],
              zoom: 11,
              behaviors: ["default", "scrollZoom"],
            }}
          >
            <Clusterer
              options={{
                preset: "islands#invertedVioletClusterIcons",
                groupByCoordinates: false,
                gridSize: 80,
              }}
            >
              {data?.data.map((item) => (
                <Placemark
                  key={item.id}
                  geometry={item.location.coordinate}
                  properties={{
                    balloonContentHeader: item.name,
                    balloonContentBody: item.phone,
                  }}
                  options={{ preset: "islands#violetIcon" }}
                />
              ))}
            </Clusterer>
          </Map>
        </YMaps>

        {data?.data.map((item) => (
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
        ))}

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
