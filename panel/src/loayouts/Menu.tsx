import { FC } from "react";
import { Link } from "react-router-dom";
import { Menu as BaseMenu } from "antd";
import type { MenuProps } from "antd";
import {
  ShoppingCartOutlined,
  ShoppingOutlined,
  MobileOutlined,
  DashboardOutlined,
  ShopOutlined,
  SettingOutlined,
} from "@ant-design/icons";

const MenuItems: MenuProps["items"] = [
  {
    key: "stats",
    label: (
      <Link to={{ pathname: "/" }} state={{ menuItem: "stats" }}>
        Статистика
      </Link>
    ),
    icon: <DashboardOutlined />,
  },
  {
    key: "orders",
    label: (
      <Link to={{ pathname: "/orders" }} state={{ menuItem: "orders" }}>
        Заказы
      </Link>
    ),
    icon: <ShoppingCartOutlined />,
  },
  {
    key: "products",
    label: "Товары",
    icon: <ShoppingOutlined />,
    children: [
      {
        key: "productAll",
        label: (
          <Link
            to={{ pathname: "/products" }}
            state={{ menuItem: "productAll" }}
          >
            Все товары
          </Link>
        ),
      },
      {
        key: "productModeration",
        label: (
          <Link
            to={{ pathname: "/products/moderation" }}
            state={{ menuItem: "productModeration" }}
          >
            Модерация
          </Link>
        ),
        disabled: true,
      },
      {
        key: "productStats",
        label: (
          <Link
            to={{ pathname: "/products/stats" }}
            state={{ menuItem: "productStats" }}
          >
            Статистика
          </Link>
        ),
      },
    ],
  },
  {
    key: "mobile",
    label: "Мобилка",
    icon: <MobileOutlined />,
    children: [
      {
        key: "mobileOrders",
        label: (
          <Link
            to={{ pathname: "/mobile/orders" }}
            state={{ menuItem: "mobileOrders" }}
          >
            Заказы
          </Link>
        ),
      },
    ],
  },
  {
    key: "offers",
    label: (
      <Link to={{ pathname: "/offers" }} state={{ menuItem: "offers" }}>
        Остатки
      </Link>
    ),
    icon: <ShopOutlined />,
  },
  {
    key: "settings",
    label: "Настройки",
    icon: <SettingOutlined />,
    children: [
      {
        key: "settingBanner",
        label: (
          <Link
            to={{ pathname: "/settings/banner" }}
            state={{ menuItem: "settingBanner" }}
          >
            Баннер
          </Link>
        ),
      },
    ],
  },
  {
    key: "kids",
    label: "Рисунки детей",
    icon: <SettingOutlined />,
    children: [
      {
        key: "activePhoto",
        label: (
          <Link
            to={{ pathname: "/kids/PhotoActive" }}
            state={{ menuItem: "settingBanner" }}
          >
            Активные
          </Link>
        ),
      },
      {
        key: "NotActivePhoto",
        label: (
          <Link
            to={{ pathname: "/kids/PhotoNotActive" }}
            state={{ menuItem: "settingBanner" }}
          >
            Неактивные
          </Link>
        ),
      },
    ],
  },
];

interface Props {
  mode?: "vertical" | "horizontal" | "inline";
  defaultSelected?: string;
}

const Menu: FC<Props> = ({ mode = "inline", defaultSelected = "stats" }) => {
  return (
    <BaseMenu
      mode={mode}
      defaultSelectedKeys={[defaultSelected]}
      items={MenuItems}
    />
  );
};

export { Menu };
