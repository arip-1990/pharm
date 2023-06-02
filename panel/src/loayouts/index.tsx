import React from "react";
import { Outlet } from "react-router-dom";
import { Layout as BaseLayout } from "antd";
import Header from "./Header";
import Sidebar from "./Sidebar";
import { SiderTheme } from "antd/lib/layout/Sider";
import moment from "moment";

const Layout: React.FC = () => {
  const [theme, setTheme] = React.useState<SiderTheme>("light");
  const [collapsed, setCollapsed] = React.useState<boolean>(false);

  const toggleCollapsed = () => setCollapsed((item) => !item);

  return (
    <BaseLayout>
      <Sidebar theme={theme} switchTheme={setTheme} collapsed={collapsed} />
      <BaseLayout className="content-layout">
        <Header
          theme={theme}
          collapsed={collapsed}
          onCollapsed={toggleCollapsed}
        />

        <BaseLayout.Content style={{ minHeight: 280, padding: "1rem 2rem" }}>
          <Outlet />
        </BaseLayout.Content>

        <BaseLayout.Footer>
          ООО «Социальная аптека» ©{moment().format("YYYY")}
        </BaseLayout.Footer>
      </BaseLayout>
    </BaseLayout>
  );
};

export default Layout;
