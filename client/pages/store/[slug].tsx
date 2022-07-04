import Layout from "../../components/layout";
import Head from "next/head";
import { Map, YMaps, Clusterer, Placemark } from "@pbe/react-yandex-maps";
import { GetServerSideProps } from "next";
import { FC } from "react";
import api from "../../services/api";
import { IStore } from "../../models/IStore";
import Page from "../../components/page";
import Image from "next/image";
import payments from "../../assets/images/payments.png";

type Props = {
  store: IStore;
};

const Store: FC<Props> = ({ store }) => {
  return (
    <Layout>
      <Head>
        <title>{store.name}</title>
      </Head>

      <Page className="row">
        <div className="col-12 col-md-6">
          <YMaps query={{ apikey: "de8de84b-e8b4-46c9-ba10-4cf2911deebf" }}>
            <Map
              width="100%"
              height={400}
              defaultState={{
                center: store.coordinate,
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
                <Placemark
                  geometry={store.coordinate}
                  properties={{
                    balloonContentHeader: store.name,
                    balloonContentBody: store.phone,
                  }}
                  options={{ preset: "islands#violetIcon" }}
                />
              </Clusterer>
            </Map>
          </YMaps>
        </div>

        <div className="col-12 col-md-6">
          <h4 className="text-center">{store.name}</h4>

          {store.route ? (
            <>
              <h5>
                <b>Как добраться:</b>
              </h5>
              <span dangerouslySetInnerHTML={{ __html: store.route }} />
            </>
          ) : null}

          <h5>
            <b>Режим работы:</b>
          </h5>
          <span dangerouslySetInnerHTML={{ __html: store.schedule }} />

          <h5>
            <b>Доставка:</b>
          </h5>
          <span>{store.delivery ? "Есть" : "Нет"}</span>

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
          <span>{store.phone}</span>
        </div>
      </Page>
    </Layout>
  );
};

export const getServerSideProps: GetServerSideProps<Props> = async (
  context
) => {
  const slug = context.query.slug as string;

  const { data } = await api.get<IStore>(`store/${slug}`);

  return {
    props: { store: data },
  };
};

export default Store;
