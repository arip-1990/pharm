import "../styles/global.scss";
import { wrapper } from "../lib/store";

const App = ({ Component, pageProps }) => {
  return <Component {...pageProps} />;
};

export default wrapper.withRedux(App);
