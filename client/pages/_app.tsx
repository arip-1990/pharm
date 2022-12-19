import React, { useEffect, useState } from "react";
import { useRouter } from "next/router";
import { Sanctum } from "react-sanctum";
import moment from "moment";

import { wrapper } from "../store";
import Loader from "../components/loader";

import "swiper/css/bundle";
import "react-notifications/lib/notifications.css";
import "../styles/global.scss";
import "moment/locale/ru";
import { API_URL } from "../lib/api";

moment.locale("ru");

const sanctumConfig = {
  apiUrl: API_URL,
  csrfCookieRoute: "sanctum/csrf-cookie",
  signInRoute: "v1/auth/login",
  signOutRoute: "v1/auth/logout",
  userObjectRoute: "v1/user",
  usernameKey: 'login'
};

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
    <Sanctum config={sanctumConfig}>
      {loading && <Loader />}
      <Component {...pageProps} />
    </Sanctum>
  );
};

export default wrapper.withRedux(App);
