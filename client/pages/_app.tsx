import React, { useEffect, useState } from "react";
import { useRouter } from "next/router";
import moment from "moment";

import { wrapper } from "../store";
import { Auth } from "../store/auth";
import Loader from "../components/loader";

import "react-notifications/lib/notifications.css";
import "../styles/global.scss";
import "moment/locale/ru";

moment.locale("ru");

const App = ({ Component, pageProps }) => {
  const router = useRouter();
  const [loading, setLoading] = useState<boolean>(false);

  useEffect(() => {
    const handleStart = () => {
      setLoading(true);
      if (document.body.offsetHeight > window.innerHeight)
        document.body.style.paddingRight = "15px";
      document.body.style.overflow = "hidden";
    };
    const handleComplete = () => {
      setLoading(false);
      document.body.removeAttribute("style");
    };

    router.events.on("routeChangeStart", handleStart);
    router.events.on("routeChangeComplete", handleComplete);
    router.events.on("routeChangeError", handleComplete);

    return () => {
      router.events.off("routeChangeStart", handleStart);
      router.events.off("routeChangeComplete", handleComplete);
      router.events.off("routeChangeError", handleComplete);
    };
  }, []);

  return (
    <Auth>
      {loading && <Loader />}
      <Component {...pageProps} />
    </Auth>
  );
};

export default wrapper.withRedux(App);
