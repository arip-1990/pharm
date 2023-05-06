import React from "react";
import { useLocation } from "react-router-dom";
import { Layout } from "antd";
import { SiderTheme } from "antd/lib/layout/Sider";
import { Menu } from "./Menu";

interface PropsType {
  theme: SiderTheme;
  collapsed: boolean;
  switchTheme: (theme: SiderTheme) => void;
}

const Sidebar: React.FC<PropsType> = ({ theme, collapsed, switchTheme }) => {
  const location = useLocation();

  return (
    <Layout.Sider
      theme={theme}
      trigger={null}
      collapsible
      collapsed={collapsed}
    >
      <div className="brand">
        <span className="logo" />
      </div>

      <Menu defaultSelected={location.state?.menuItem || "stats"} />

      {/* {!collapsed ? (
        <div className="switch-theme">
          <span>
            <BulbOutlined /> Темная тема
          </span>
          <Switch
            checkedChildren="Вкл"
            unCheckedChildren="Выкл"
            onClick={(checked) => switchTheme(checked ? "dark" : "light")}
          />
        </div>
      ) : null} */}
    </Layout.Sider>
  );
};

export default Sidebar;
