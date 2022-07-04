import { GetServerSideProps } from "next";
import { Map, YMaps, Clusterer, Placemark } from "@pbe/react-yandex-maps";
import { FC } from "react";
import Layout from "../components/layout";
import Page from "../components/page";
import { IStore } from "../models/IStore";
import api from "../services/api";
import Paginate from "../components/Paginate";
import Link from "next/link";

type Props = {
  stories: Pagination<IStore>;
};

const Store: FC<Props> = ({ stories }) => {
  return (
    <Layout>
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
              {stories.data.map((item) => (
                <Placemark
                  key={item.id}
                  geometry={item.coordinate}
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

        {stories.data.map((item) => (
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
              <Link href={`store/${item.slug}`}>
                <a className="btn btn-sm btn-primary">Посмотреть</a>
              </Link>
            </div>
          </div>
        ))}

        <Paginate
          current={stories.meta.current_page}
          total={stories.meta.total}
        />
      </Page>
    </Layout>
  );
};

export const getServerSideProps: GetServerSideProps<Props> = async (
  context
) => {
  const page = context.query.page as string;

  const { data } = await api.get<Pagination<IStore>>("store", {
    params: { page },
  });

  return {
    props: { stories: data },
  };
};

export default Store;
