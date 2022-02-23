import React from "react";
import { Layout, Menu } from "antd";
import {
  MenuFoldOutlined,
  MenuUnfoldOutlined,
  LogoutOutlined,
} from "@ant-design/icons";
import { SiderTheme } from "antd/lib/layout/Sider";
import { useSanctum } from "react-sanctum";

interface PropsType {
  theme: SiderTheme;
  collapsed: boolean;
  onCollapsed: () => void;
}

const Header: React.FC<PropsType> = ({ theme, collapsed, onCollapsed }) => {
  const { user, signOut } = useSanctum();
  const [currentMenu, setCurrentMenu] = React.useState<string>('');

  const handleClickMenu = (e: any) => {
    setCurrentMenu(e.key);
    if (e.key === 'logout') signOut();
  }

  return (
    <Layout.Header>
      {React.createElement(collapsed ? MenuUnfoldOutlined : MenuFoldOutlined, {
        className: "trigger",
        onClick: onCollapsed,
      })}

      <Menu theme={theme} style={{lineHeight: '64px'}} onClick={handleClickMenu} selectedKeys={[currentMenu]} mode='horizontal'>
        <Menu.SubMenu key='profile' title={user.name}>
          <Menu.Item key='logout'>
            <LogoutOutlined /> Выход
          </Menu.Item>
        </Menu.SubMenu>
      </Menu>
    </Layout.Header>
  );
};

export default Header;
