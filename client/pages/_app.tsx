import "../styles/global.scss";
import { wrapper } from "../lib/store";
import { Auth } from "../lib/auth";

const App = ({ Component, pageProps }) => {
  return (
    <Auth>
      <Component {...pageProps} />
    </Auth>
  );
};

export default wrapper.withRedux(App);
