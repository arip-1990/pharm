import { CookiesProvider } from "react-cookie";
import { wrapper } from "../lib/store";
import { Auth } from "../lib/auth";
import "../styles/global.scss";
import { Alert } from "../lib/alert";

const App = ({ Component, pageProps }) => {
  return (
    <CookiesProvider>
      <Auth>
        <Alert>
          <Component {...pageProps} />
        </Alert>
      </Auth>
    </CookiesProvider>
  );
};

export default wrapper.withRedux(App);
