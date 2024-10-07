import { FC, Suspense, useEffect } from "react";
import { Routes, Route, useNavigate, useLocation } from "react-router-dom";
import { Spin } from "antd";
import { LoadingOutlined } from "@ant-design/icons";
import Login from "./pages/Login";
import { useAuth } from "./hooks/useAuth";

import Layout from "./loayouts";
import Main from "./pages/Main";
import Order from "./pages/order";
import OrderView from "./pages/order/View";
import Product from "./pages/product";
import ProductView from "./pages/product/View";
import Offer from "./pages/offer";
import OfferView from "./pages/offer/View";
import Moderation from "./pages/product/moderation";
import MobileOrder from "./pages/mobile/Order";
import Statistic from "./pages/product/Statistic";
import Banner from "./pages/settings/Banner";
import ActivePhoto from "./pages/kids/ActivePhoto";
import NotActivePhoto from "./pages/kids/NotActivePhoto";

const Loader = () => <Spin size="large" indicator={<LoadingOutlined spin />} />;

const App: FC = () => {
  const { isAuth } = useAuth();
  const navigate = useNavigate();
  const location = useLocation();

  useEffect(() => {
    if (isAuth)
      navigate(location.pathname.includes("login") ? "/" : location.pathname, {
        state: location.state,
      });
    else if (isAuth === false) navigate("/login");
    }, [isAuth]);

  // }, [isAuth, location.pathname, location.state, navigate]);

  return (
    <Suspense fallback={<Loader />}>
      <Routes>
        {isAuth ? (
          <Route path="/" element={<Layout />}>
            <Route index element={<Main />} />
            <Route path="orders">
              <Route index element={<Order />} />
              <Route path=":id" element={<OrderView />} />
            </Route>
            <Route path="offers">
              <Route index element={<Offer />} />
              <Route path=":slug" element={<OfferView />} />
            </Route>
            <Route path="products">
              <Route index element={<Product />} />
              <Route path=":slug" element={<ProductView />} />
              <Route path="moderation" element={<Moderation />} />
              <Route path="stats" element={<Statistic />} />
            </Route>
            <Route path="mobile">
              <Route index element={<MobileOrder />} />
              <Route path="orders" element={<MobileOrder />} />
            </Route>
            <Route path="settings">
              {/*<Route index element={<Banner />} />*/}
              <Route path="banner" element={<Banner />} />
            </Route>

            <Route path="kids">
              <Route path="PhotoActive" element={<ActivePhoto />}/>
              <Route path="PhotoNotActive" element={<NotActivePhoto />}/>
            </Route>

          </Route>
        ) : (
          <Route path="/login" element={<Login />} />
        )}
      </Routes>
    </Suspense>
  );
};

export default App;
