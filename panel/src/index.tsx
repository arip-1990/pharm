import ReactDOM from "react-dom";
import { BrowserRouter } from "react-router-dom";
import { Provider } from "react-redux";
import { Sanctum } from "react-sanctum";
import moment from "moment";
import "moment/locale/ru";

import { store } from "./store";
import App from "./App";

import 'antd/dist/antd.css';
import "./sass/index.scss";

moment.locale("ru");

const sanctumConfig = {
  apiUrl: "http://pharm.test",
  csrfCookieRoute: "sanctum/csrf-cookie",
  signInRoute: "api/login",
  signOutRoute: "api/logout",
  userObjectRoute: "api/user",
};

ReactDOM.render(
  <Provider store={store}>
    <BrowserRouter>
      <Sanctum config={sanctumConfig}>
        <App />
      </Sanctum>
    </BrowserRouter>
  </Provider>,
  document.getElementById("root")
);
