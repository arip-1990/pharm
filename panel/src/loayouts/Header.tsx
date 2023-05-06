import { FC, createElement, useEffect, useState } from "react";
import { Layout, Menu } from "antd";
import { SiderTheme } from "antd/lib/layout/Sider";
import type { MenuProps } from "antd";
import { SelectInfo } from "rc-menu/lib/interface";
import {
  MenuFoldOutlined,
  MenuUnfoldOutlined,
  LogoutOutlined,
  UserOutlined,
} from "@ant-design/icons";

import { useAuth } from "../hooks/useAuth";

interface PropsType {
  theme: SiderTheme;
  collapsed: boolean;
  onCollapsed: () => void;
}

const Header: FC<PropsType> = ({ theme, collapsed, onCollapsed }) => {
  const { user, logout } = useAuth();
  const [menuItems, setMenuItems] = useState<MenuProps["items"]>([]);
  const [currentMenu, setCurrentMenu] = useState<string>("");

  useEffect(() => {
    if (user) {
      setMenuItems([
        {
          key: "profile",
          label:
            user.first_name +
            (user.last_name ? user.last_name.charAt(0) + "." : ""),
          icon: <UserOutlined />,
          children: [
            {
              key: "logout",
              label: "Выход",
              icon: <LogoutOutlined />,
            },
          ],
        },
      ]);
    }
  }, [user]);

  const handleSelectMenu = (data: SelectInfo) => {
    setCurrentMenu(data.key);
    if (data.key === "logout") logout();
  };

  return (
    <Layout.Header>
      {createElement(collapsed ? MenuUnfoldOutlined : MenuFoldOutlined, {
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
        onSelect={handleSelectMenu}
        selectedKeys={[currentMenu]}
        mode="horizontal"
        items={menuItems}
      />
    </Layout.Header>
  );
};

export default Header;
