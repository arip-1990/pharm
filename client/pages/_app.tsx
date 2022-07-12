import "../styles/global.scss";
import { Sanctum } from "react-sanctum";
import { API_URL } from "../services/api";
import { wrapper } from "../services/store";

const sanctumConfig = {
  apiUrl: API_URL,
  csrfCookieRoute: "sanctum/csrf-cookie",
  signInRoute: "v2/login",
  signOutRoute: "v2/logout",
  userObjectRoute: "v2/user",
};

const App = ({ Component, pageProps }) => {
  return (
    <Sanctum config={sanctumConfig}>
      <Component {...pageProps} />
    </Sanctum>
  );
};

export default wrapper.withRedux(App);
