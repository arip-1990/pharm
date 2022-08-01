import Layout from "../../components/layout";
import Head from "next/head";
import { Map, YMaps, Clusterer, Placemark } from "@pbe/react-yandex-maps";
import { GetServerSideProps } from "next";
import { FC } from "react";
import Page from "../../components/page";
import Image from "next/image";
import payments from "../../assets/images/payments.png";
import { wrapper } from "../../lib/store";
import {
  getRunningOperationPromises,
  getStore,
  useGetStoreQuery,
} from "../../lib/storeService";
import { useRouter } from "next/router";

const Store: FC = () => {
  const router = useRouter();
  const { slug } = router.query;
  const { data, isFetching } = useGetStoreQuery(
    typeof slug === "string" ? slug : slug[0]
  );

  return (
    <Layout>
      <Head>
        <title>{data?.name}</title>
        <meta key="description" name="description" content={data?.name} />
      </Head>

      <Page className="row">
        <div className="col-12 col-md-6">
          <YMaps query={{ apikey: "de8de84b-e8b4-46c9-ba10-4cf2911deebf" }}>
            <Map
              width="100%"
              height={400}
              defaultState={{
                center: [0, 0], //data?.coordinate,
                zoom: 17,
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
                {/* <Placemark
                  geometry={data?.coordinate}
                  properties={{
                    balloonContentHeader: data?.name,
                    balloonContentBody: data?.phone,
                  }}
                  options={{ preset: "islands#violetIcon" }}
                /> */}
              </Clusterer>
            </Map>
          </YMaps>
        </div>

        <div className="col-12 col-md-6">
          <h4 className="text-center">{data?.name}</h4>

          {data?.route ? (
            <>
              <h5>
                <b>Как добраться:</b>
              </h5>
              <span dangerouslySetInnerHTML={{ __html: data?.route }} />
            </>
          ) : null}

          <h5>
            <b>Режим работы:</b>
          </h5>
          <span dangerouslySetInnerHTML={{ __html: data?.schedule }} />

          <h5>
            <b>Доставка:</b>
          </h5>
          <span>{data?.delivery ? "Есть" : "Нет"}</span>

          <h5>
            <b>Способ оплаты:</b>
          </h5>
          <span>
            картой{" "}
            <Image
              src={payments}
              height={20}
              alt="Мир, Visa, MasterCard, Maestro"
            />
            , наличными
          </span>

          <h5>
            <b>Контакты:</b>
          </h5>
          <span>{data?.phone}</span>
        </div>
      </Page>
    </Layout>
  );
};

export const getServerSideProps: GetServerSideProps = wrapper.getServerSideProps(
  (store) => async () => {
    store.dispatch(getStore.initiate());

    await Promise.all(getRunningOperationPromises());

    return { props: {} };
  }
);

export default Store;
