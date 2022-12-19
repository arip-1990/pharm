import ReactDOM from "react-dom";
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

import "antd/dist/antd.css";
import "./sass/index.scss";

moment.locale("ru");

const sanctumConfig = {
  apiUrl: API_URL,
  csrfCookieRoute: "sanctum/csrf-cookie",
  signInRoute: "v1/panel/auth/login",
  signOutRoute: "v1/panel/auth/logout",
  userObjectRoute: "v1/panel/auth/user"
};

ReactDOM.render(
  <Provider store={store}>
    <BrowserRouter>
      <Sanctum config={sanctumConfig}>
        <ConfigProvider locale={ruRU}>
          <App />
        </ConfigProvider>
      </Sanctum>
    </BrowserRouter>
  </Provider>,
  document.getElementById("root")
);
