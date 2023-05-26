import React from "react";
import { Sanctum } from "react-sanctum";
import moment from "moment";

import { wrapper } from "../store";

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
  usernameKey: "login",
};

const App = ({ Component, pageProps }) => (
  <Sanctum config={sanctumConfig}>
    <Component {...pageProps} />
  </Sanctum>
);

export default wrapper.withRedux(App);
