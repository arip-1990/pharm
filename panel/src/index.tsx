import ReactDOM from "react-dom";
import { BrowserRouter } from "react-router-dom";
import { Provider } from "react-redux";
import { ConfigProvider } from "antd";
import ruRU from "antd/lib/locale/ru_RU";
import moment from "moment";
import "moment/locale/ru";

import { store } from "./store";
import { Auth } from "./services/auth";
import App from "./App";

import "antd/dist/antd.css";
import "./sass/index.scss";

moment.locale("ru");

ReactDOM.render(
  <Provider store={store}>
    <BrowserRouter>
      <Auth>
        <ConfigProvider locale={ruRU}>
          <App />
        </ConfigProvider>
      </Auth>
    </BrowserRouter>
  </Provider>,
  document.getElementById("root")
);
