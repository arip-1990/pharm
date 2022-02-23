import React from "react";
import { Outlet } from "react-router-dom";
import { Layout as BaseLayout } from "antd";
import Header from "./Header";
import Sidebar from "./Sidebar";
import { SiderTheme } from "antd/lib/layout/Sider";
import classnames from "classnames";
import moment from "moment";

const Layout: React.FC = () => {
  const [theme, setTheme] = React.useState<SiderTheme>('light');
  const [collapsed, setCollapsed] = React.useState<boolean>(false);

  const toggleCollapsed = () => setCollapsed(item => !item);

  return (
    <BaseLayout className={classnames({'collapsed': collapsed})}>
      <Sidebar theme={theme} switchTheme={setTheme} collapsed={collapsed} />
      <BaseLayout className="content-layout">
        <Header theme={theme} collapsed={collapsed} onCollapsed={toggleCollapsed} />

        <BaseLayout.Content style={{margin: '24px 16px', padding: 24, minHeight: 280}}><Outlet /></BaseLayout.Content>

        <BaseLayout.Footer>ООО «Социальная аптека» ©{moment().format('YYYY')}</BaseLayout.Footer>
      </BaseLayout>
    </BaseLayout>
  );
};

export default Layout;
