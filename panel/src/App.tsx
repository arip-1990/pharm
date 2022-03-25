import React from "react";
import { Routes, Route, useNavigate, useLocation } from "react-router-dom";
import { Spin } from "antd";
import { LoadingOutlined } from "@ant-design/icons";
import { useSanctum } from "react-sanctum";
import privateRoute from './privateRoute';
import Login from "./pages/Login";

const Loader = () => <Spin size='large' indicator={<LoadingOutlined spin />} />

export default () => {
  const { authenticated } = useSanctum();
  const navigate = useNavigate();
  const location = useLocation();

  React.useEffect(() => {
    if (authenticated === false) navigate('/login');
    else if (authenticated === true) navigate(location.pathname, {state: location.state});
  }, [authenticated]);

  return (
    <React.Suspense fallback={<Loader />}>
      <Routes>
        {authenticated ? privateRoute()  : <Route path='/login' element={<Login />} />}
      </Routes>
    </React.Suspense>
  );
};
