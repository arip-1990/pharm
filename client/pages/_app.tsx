import { CookiesProvider } from "react-cookie";
import { wrapper } from "../lib/store";
import { Auth } from "../lib/auth";
import "../styles/global.scss";

const App = ({ Component, pageProps }) => {
  return (
    <CookiesProvider>
      <Auth>
        <Component {...pageProps} />
      </Auth>
    </CookiesProvider>
  );
};

export default wrapper.withRedux(App);
