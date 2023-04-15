import React from "react";
import { Link, useLocation } from "react-router-dom";
import { Layout, Menu, Switch } from "antd";
import {
  ShoppingCartOutlined,
  ShoppingOutlined,
  MobileOutlined,
  DashboardOutlined,
  ShopOutlined,
} from "@ant-design/icons";
import { SiderTheme } from "antd/lib/layout/Sider";

interface PropsType {
  theme: SiderTheme;
  collapsed: boolean;
  switchTheme: (theme: SiderTheme) => void;
}

const Sidebar: React.FC<PropsType> = ({ theme, collapsed, switchTheme }) => {
  const { state }: any = useLocation();

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

      <Menu mode="inline" defaultSelectedKeys={state?.menuItem || ["stats"]}>
        <Menu.Item key="stats" icon={<DashboardOutlined />}>
          <Link to="/" state={{ menuItem: ["stats"] }}>
            Статистика
          </Link>
        </Menu.Item>
        <Menu.Item key="order" icon={<ShoppingCartOutlined />}>
          <Link to="order" state={{ menuItem: ["order"] }}>
            Заказы
          </Link>
        </Menu.Item>
        <Menu.SubMenu title="Товары" icon={<ShoppingOutlined />}>
          <Menu.Item key="product">
            <Link to="product" state={{ menuItem: ["product"] }}>
              Все товары
            </Link>
          </Menu.Item>
          <Menu.Item key="moderation">
            <Link to="/product/moderation" state={{ menuItem: ["moderation"] }}>
              Модерация
            </Link>
          </Menu.Item>
          <Menu.Item key="productStats">
            <Link to="/product/stats" state={{ menuItem: ["productStats"] }}>
              Статистика
            </Link>
          </Menu.Item>
        </Menu.SubMenu>
        <Menu.SubMenu title="Мобилка" icon={<MobileOutlined />}>
          <Menu.Item key="mobile_order">
            <Link to="mobile/order" state={{ menuItem: ["mobile_order"] }}>
              Заказы
            </Link>
          </Menu.Item>
        </Menu.SubMenu>
        <Menu.Item key="offer" icon={<ShopOutlined />}>
          <Link to="offer" state={{ menuItem: ["offer"] }}>
            Остатки
          </Link>
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
