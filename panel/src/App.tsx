import React from "react";
import { Routes, Route, useNavigate, useLocation } from "react-router-dom";
import { Spin } from "antd";
import { LoadingOutlined } from "@ant-design/icons";
import privateRoute from "./privateRoute";
import Login from "./pages/Login";
import { useAuth } from "./hooks/useAuth";

const Loader = () => <Spin size="large" indicator={<LoadingOutlined spin />} />;

export default () => {
  const { isAuth } = useAuth();
  const navigate = useNavigate();
  const location = useLocation();

  React.useEffect(() => {
    const path = location.pathname === "/login" ? "/" : location.pathname;
    if (isAuth) navigate(path, { state: location.state });
    else if (isAuth === false) navigate("/login");
  }, [isAuth]);

  return (
    <React.Suspense fallback={<Loader />}>
      <Routes>
        {isAuth ? privateRoute() : <Route path="/login" element={<Login />} />}
      </Routes>
    </React.Suspense>
  );
};
