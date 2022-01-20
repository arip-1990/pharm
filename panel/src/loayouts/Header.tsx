import React from "react";
import { Layout, Menu } from "antd";
import {
  MenuFoldOutlined,
  MenuUnfoldOutlined,
  LogoutOutlined,
} from "@ant-design/icons";
import { SiderTheme } from "antd/lib/layout/Sider";
import classnames from "classnames";
import { useSanctum } from "react-sanctum";

interface PropsType {
  theme: SiderTheme;
  collapsed: boolean;
  onCollapsed: () => void;
}

const Header: React.FC<PropsType> = ({ theme, collapsed, onCollapsed }) => {
  const { user, signOut } = useSanctum();

  return (
    <Layout.Header
      className={classnames(theme, {
        "ant-layout-header-collapsed": collapsed,
      })}
    >
      {React.createElement(collapsed ? MenuUnfoldOutlined : MenuFoldOutlined, {
        className: "trigger",
        onClick: onCollapsed,
      })}
      <Menu theme={theme} mode="horizontal">
        <Menu.SubMenu key="SubMenu" title={user.name}>
          <Menu.Item key="logout" onClick={signOut}>
            <LogoutOutlined /> Выход
          </Menu.Item>
        </Menu.SubMenu>
      </Menu>
    </Layout.Header>
  );
};

export default Header;
