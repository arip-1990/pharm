import React, { FC, useEffect, useState } from "react";
import { GetServerSideProps } from "next";
import { useRouter } from "next/router";

import { wrapper } from "../store";
import Auth from "../components/auth";
import { useCookie } from "../hooks/useCookie";
import Layout from "../templates";
import Card from "../components/card";
import { fetchPopulars, getRunningQueriesThunk, useFetchPopularsQuery } from "../lib/productService";

type AuthType = "login" | "register";

const Home: FC = () => {
  const [authModal, setAuthModal] = useState<{
    type: AuthType;
    show: boolean;
  }>({ type: "login", show: false });
  const [city] = useCookie("city");
  const { data, isFetching, refetch } = useFetchPopularsQuery();
  const router = useRouter();

  useEffect(() => {
    const path = router.asPath.split("#");
    if (path.length > 1 && ["login", "register"].includes(path[1])) {
      const hash = path[1] as AuthType;
      setAuthModal({ type: hash, show: true });
    }
  }, []);

  useEffect(() => {
    refetch();
  }, [city]);

  return (
    <Layout loading={isFetching} banner title="Сеть аптек 120/80">
      <div
        className="row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-4 g-3 g-xl-4"
        itemScope
        itemType="https://schema.org/ItemList"
      >
        <link itemProp="url" href="/" />
        {data?.map((product) => (
          <div key={product.id} className="col-10 offset-1 offset-sm-0">
            <Card product={product} />
          </div>
        ))}
      </div>

      <Auth
        show={authModal.show}
        type={authModal.type}
        onHide={() => setAuthModal((item) => ({ ...item, show: false }))}
      />
    </Layout>
  );
};

export const getServerSideProps: GetServerSideProps = wrapper.getServerSideProps(
  (store) => async () => {
    store.dispatch(fetchPopulars.initiate());

    await Promise.all(store.dispatch(getRunningQueriesThunk()));

    return { props: {} };
  }
);

export default Home;
