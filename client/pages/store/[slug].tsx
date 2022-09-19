import Layout from "../../components/layout";
import Head from "next/head";
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
import Map from "../../components/Map";

const Store: FC = () => {
  const router = useRouter();
  const { slug } = router.query;
  const { data } = useGetStoreQuery(String(slug));

  return (
    <Layout>
      <Head>
        <title>{data?.name}</title>
        <meta key="description" name="description" content={data?.name} />
      </Head>

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
          <h4 className="text-center">
            <b>{data?.location.city},</b> {data?.location.address}
          </h4>

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
  (store) => async ({ params }) => {
    const { slug } = params;

    store.dispatch(getStore.initiate(String(slug)));

    await Promise.all(getRunningOperationPromises());

    return { props: {} };
  }
);

export default Store;
