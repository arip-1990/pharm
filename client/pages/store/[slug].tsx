import Layout from "../../templates";
import { GetServerSideProps } from "next";
import { FC, useCallback } from "react";
import Page from "../../components/page";
import Image from "next/image";
import payments from "../../assets/images/payments.png";
import { wrapper } from "../../store";
import {
  getRunningOperationPromises,
  getStore,
  useGetStoreQuery,
} from "../../lib/storeService";
import { useRouter } from "next/router";
import Map from "../../components/Map";
import Breadcrumbs from "../../components/breadcrumbs";

const Store: FC = () => {
  const router = useRouter();
  const { slug } = router.query;
  const { data } = useGetStoreQuery(String(slug));

  const getDefaultGenerator = useCallback(
    () => [
      { href: "/store", text: "Точки самовывоза" },
      { href: `/store/${String(slug)}`, text: data?.name },
    ],
    [data]
  );

  return (
    <Layout
      title={data ? `${data?.name} - Сеть аптек 120/80` : "Сеть аптек 120/80"}
    >
      <Breadcrumbs getDefaultGenerator={getDefaultGenerator} />

      <Page className="row">
        <div className="col-12 col-md-6">
          <Map
            center={data?.location.coordinate}
            points={[
              {
                title: data?.name,
                description: data?.phone,
                coordinates: data?.location.coordinate,
              },
            ]}
          />
        </div>

        <div className="col-12 col-md-6">
          <h5 className="text-center">{data?.location.address}</h5>

          {data?.route ? (
            <>
              <h6>
                <b>Как добраться:</b>
              </h6>
              <span dangerouslySetInnerHTML={{ __html: data?.route }} />
            </>
          ) : null}

          <h6>
            <b>Режим работы:</b>
          </h6>
          <span dangerouslySetInnerHTML={{ __html: data?.schedule }} />

          <h6>
            <b>Доставка:</b>
          </h6>
          <span>{data?.delivery ? "Есть" : "Нет"}</span>

          <h6>
            <b>Способ оплаты:</b>
          </h6>
          <span>
            картой{" "}
            <Image
              src={payments}
              height={20}
              alt="Мир, Visa, MasterCard, Maestro"
            />
            , наличными
          </span>

          <h6>
            <b>Контакты:</b>
          </h6>
          <span>{data?.phone}</span>
        </div>
      </Page>
    </Layout>
  );
};

export const getServerSideProps: GetServerSideProps = wrapper.getServerSideProps(
  (store) => async ({ params }) => {
    const { slug } = params;

    store.dispatch(getStore.initiate(String(slug)));

    await Promise.all(getRunningOperationPromises());

    return { props: {} };
  }
);

export default Store;
