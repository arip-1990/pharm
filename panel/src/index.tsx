import { createRoot } from "react-dom/client";
import { BrowserRouter } from "react-router-dom";
import { Provider } from "react-redux";
import { ConfigProvider } from "antd";
import { Sanctum } from "react-sanctum";
import ruRU from "antd/lib/locale/ru_RU";
import moment from "moment";
import "moment/locale/ru";

import { API_URL } from "./services/api";
import { store } from "./store";
import App from "./App";

import "antd/dist/reset.css";
import "./sass/index.scss";

moment.locale("ru");
moment.defaultFormat = "YYYY-MM-DDTHH:mm:ss.SSZZ";

const sanctumConfig = {
  apiUrl: API_URL,
  csrfCookieRoute: "sanctum/csrf-cookie",
  signInRoute: "v1/panel/auth/login",
  signOutRoute: "v1/panel/auth/logout",
  userObjectRoute: "v1/panel/auth/user",
};

const root = createRoot(document.getElementById("root") as HTMLElement);

root.render(
  <Provider store={store}>
    <BrowserRouter>
      <Sanctum config={sanctumConfig}>
        <ConfigProvider locale={ruRU}>
          <App />
        </ConfigProvider>
      </Sanctum>
    </BrowserRouter>
  </Provider>
);
