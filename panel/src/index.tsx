import ReactDOM from "react-dom";
import { BrowserRouter } from "react-router-dom";
import { Provider } from "react-redux";
import { Sanctum } from "react-sanctum";
import { ConfigProvider } from 'antd';
import ruRU from 'antd/lib/locale/ru_RU';
import moment from "moment";
import "moment/locale/ru";
import { API_URL } from "./services/api";

import { store } from "./store";
import App from "./App";

import 'antd/dist/antd.css';
import "./sass/index.scss";

moment.locale("ru");

const sanctumConfig = {
  apiUrl: API_URL,
  csrfCookieRoute: "sanctum/csrf-cookie",
  signInRoute: "api/v1/login",
  signOutRoute: "api/v1/logout",
  userObjectRoute: "api/v1/user",
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
