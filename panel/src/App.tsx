import React from "react";
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

const Loader = () => <Spin size="large" indicator={<LoadingOutlined spin />} />;

export default () => {
  const { isAuth } = useAuth();
  const navigate = useNavigate();
  const location = useLocation();

  React.useEffect(() => {
    if (isAuth)
      navigate(location.pathname.includes("login") ? "/" : location.pathname, {
        state: location.state,
      });
    else if (isAuth === false) navigate("/login");
  }, [isAuth]);

  return (
    <React.Suspense fallback={<Loader />}>
      <Routes>
        {isAuth ? (
          <Route path="/" element={<Layout />}>
            <Route index element={<Main />} />
            <Route path="order">
              <Route index element={<Order />} />
              <Route path=":id" element={<OrderView />} />
            </Route>
            <Route path="offer">
              <Route index element={<Offer />} />
              <Route path=":slug" element={<OfferView />} />
            </Route>
            <Route path="product">
              <Route index element={<Product />} />
              <Route path=":slug" element={<ProductView />} />
              <Route path="moderation" element={<Moderation />} />
              <Route path="stats" element={<Statistic />} />
            </Route>
            <Route path="mobile">
              <Route index element={<MobileOrder />} />
              <Route path="order" element={<MobileOrder />} />
            </Route>
          </Route>
        ) : (
          <Route path="/login" element={<Login />} />
        )}
      </Routes>
    </React.Suspense>
  );
};
