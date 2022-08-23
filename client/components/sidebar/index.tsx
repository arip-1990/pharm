import Link from "next/link";
import { FC, MouseEvent } from "react";
import classNames from "classnames";
import { useRouter } from "next/router";
import styles from "./Sidebar.module.scss";
import { useAuth } from "../../hooks/useAuth";

type Props = {
  className?: string;
};

const Sidebar: FC<Props> = ({ className }) => {
  const router = useRouter();
  const { logout } = useAuth();
  let classes = [];
  if (className) classes = classes.concat(className.split(" "));

  const handleSignOut = (e: MouseEvent) => {
    e.preventDefault();
    logout();
  };

  return (
    <nav className={classes.join(" ")}>
      <Link href="/profile/order">
        <a
          className={classNames(styles.sidebar_item, {
            [styles.active]: router.pathname.startsWith("/profile/order"),
          })}
        >
          Мои заказы
        </a>
      </Link>
      <Link href="/profile/orderProduct">
        <a
          className={classNames(styles.sidebar_item, {
            [styles.active]: router.pathname.startsWith(
              "/profile/orderProduct"
            ),
          })}
        >
          Товары, которые вы покупали
        </a>
      </Link>
      <Link href="/profile/setting">
        <a
          className={classNames(styles.sidebar_item, {
            [styles.active]: router.pathname.startsWith("/profile/setting"),
          })}
        >
          Настройки аккаунта
        </a>
      </Link>
      <div className="border-top my-3" />
      <a href="/" className={styles.sidebar_item} onClick={handleSignOut}>
        Выйти из аккаунта
      </a>
    </nav>
  );
};

export default Sidebar;
