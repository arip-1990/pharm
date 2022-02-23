import React from "react";
import { Layout, Menu, Switch } from "antd";
import {
  ShoppingCartOutlined,
  ShoppingOutlined,
  BulbOutlined,
  DashboardOutlined,
} from "@ant-design/icons";
import { Link } from "react-router-dom";
import { SiderTheme } from "antd/lib/layout/Sider";

interface PropsType {
  theme: SiderTheme;
  collapsed: boolean;
  switchTheme: (theme: SiderTheme) => void;
}

const Sidebar: React.FC<PropsType> = ({ theme, collapsed, switchTheme }) => {
  return (
    <Layout.Sider theme={theme} trigger={null} collapsible collapsed={collapsed}>
      <div className="brand">
        <span className="logo" />
      </div>
      <Menu mode="inline" defaultSelectedKeys={["stats"]}>
        <Menu.Item key="stats" icon={<DashboardOutlined />}>
          <Link to="/">Статистика</Link>
        </Menu.Item>
        <Menu.Item key="order" icon={<ShoppingCartOutlined />}>
          <Link to="order">Заказы</Link>
        </Menu.Item>
        <Menu.Item key="Product" icon={<ShoppingOutlined />}>
          <Link to="product">Товары</Link>
        </Menu.Item>
      </Menu>
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
