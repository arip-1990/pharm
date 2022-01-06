import React from "react";
import { Routes, Route, useNavigate, useLocation } from "react-router-dom";
import { Spin } from "antd";
import { LoadingOutlined } from "@ant-design/icons";
import { useSanctum } from "react-sanctum";
import Login from "./pages/Login";
import Layout from "./loayouts";
import Main from "./pages/Main";
import Order from "./pages/Order";
import Product from "./pages/product";
import ProductView from "./pages/product/View";

const Loader = () => <Spin size='large' indicator={<LoadingOutlined spin />} />

export default () => {
  const { authenticated } = useSanctum();
  const navigate = useNavigate();
  const location = useLocation();

  React.useEffect(() => {
    if (authenticated === false) navigate('/login');
    else if (authenticated === true) navigate('/');
  }, [authenticated]);
  
  return (
    <React.Suspense fallback={<Loader />}>
      <Routes>
          {authenticated === true ? <Route path='/' element={<Layout />}>
              <Route index element={<Main />} />
              <Route path='order' element={<Order />} />
              <Route path='product' element={<Product />} />
              <Route path='product/:slug' element={<ProductView />} />
          </Route> : <Route path='/login' element={<Login />} />}
        </Routes>
    </React.Suspense>
  );
};
