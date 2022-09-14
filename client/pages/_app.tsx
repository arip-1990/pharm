import { wrapper } from "../lib/store";
import { Auth } from "../lib/auth";
import "react-notifications/lib/notifications.css";
import "../styles/global.scss";

const App = ({ Component, pageProps }) => {
  return (
    <Auth>
      <Component {...pageProps} />
    </Auth>
  );
};

export default wrapper.withRedux(App);
