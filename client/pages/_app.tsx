import "../styles/global.scss";
import { Sanctum } from "react-sanctum";
import { API_URL } from "../services/api";
import { Provider } from "react-redux";
import { store } from "../services/store";

const sanctumConfig = {
  apiUrl: API_URL,
  csrfCookieRoute: "sanctum/csrf-cookie",
  signInRoute: "v2/login",
  signOutRoute: "v2/logout",
  userObjectRoute: "v2/user",
};

export default ({ Component, pageProps }) => {
  return (
    <Sanctum config={sanctumConfig}>
      <Provider store={store}>
        <Component {...pageProps} />
      </Provider>
    </Sanctum>
  );
};
