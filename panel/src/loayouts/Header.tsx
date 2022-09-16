import React from "react";
import { Layout, Menu } from "antd";
import {
  MenuFoldOutlined,
  MenuUnfoldOutlined,
  LogoutOutlined,
  UserOutlined,
} from "@ant-design/icons";
import { SiderTheme } from "antd/lib/layout/Sider";
import { useAuth } from "../hooks/useAuth";

interface PropsType {
  theme: SiderTheme;
  collapsed: boolean;
  onCollapsed: () => void;
}

const Header: React.FC<PropsType> = ({ theme, collapsed, onCollapsed }) => {
  const { user, logout } = useAuth();
  const [currentMenu, setCurrentMenu] = React.useState<string>("");

  const handleClickMenu = (e: any) => {
    setCurrentMenu(e.key);
    if (e.key === "logout") logout();
  };

  return (
    <Layout.Header>
      {React.createElement(collapsed ? MenuUnfoldOutlined : MenuFoldOutlined, {
        className: "trigger",
        onClick: onCollapsed,
      })}

      <Menu
        theme={theme}
        style={{
          display: "flex",
          flex: 0.5,
          justifyContent: "end",
          lineHeight: "64px",
        }}
        onClick={handleClickMenu}
        selectedKeys={[currentMenu]}
        mode="horizontal"
      >
        <Menu.SubMenu
          key="profile"
          icon={<UserOutlined />}
          title={
            user?.first_name + (user?.last_name ? ` ${user.last_name[0]}` : "")
          }
        >
          <Menu.Item key="logout">
            <LogoutOutlined /> Выход
          </Menu.Item>
        </Menu.SubMenu>
      </Menu>
    </Layout.Header>
  );
};

export default Header;
