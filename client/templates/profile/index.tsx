import classNames from "classnames";
import Link from "next/link";
import { useRouter } from "next/router";
import { FC, MouseEvent, ReactNode, useEffect } from "react";
import { Table } from "../../components/table";
import { useAuth } from "../../hooks/useAuth";

import styles from "./Profile.module.scss";

interface Props {
  title?: string;
  className?: string;
  children?: ReactNode;
  contentClassName?: string;
}

const Profile: FC<Props> = ({
  title,
  className,
  children,
  contentClassName,
}) => {
  const { isAuth, user, logout } = useAuth();
  const router = useRouter();
  let classes = [styles.profile];
  if (className) classes = classes.concat(className.split(" "));

  useEffect(() => {
    if (isAuth === false) router.replace("/");
  }, [isAuth]);

  const handleLogout = async (e: MouseEvent) => {
    e.preventDefault();
    try {
      await logout();
      router.replace("/");
    } catch (error) {
      console.log(error);
    }
  };

  return (
    <section className={classes.join(" ")}>
      <aside className={styles.profile_sidebar}>
        <nav className={styles["profile_sidebar-menu"]}>
          <Link href="/">
            <a>Главная</a>
          </Link>
          <Link href="/profile/cheque">
            <a>Покупки</a>
          </Link>
          <Link href="/profile/coupon">
            <a>Купоны</a>
          </Link>
          <Link href="/profile">
            <a>Анкета</a>
          </Link>
          <a href="#" onClick={handleLogout}>
            Выход
          </a>
        </nav>

        <div className={styles["profile_sidebar-card"]}></div>

        <ul className={styles["profile_sidebar-sub-menu"]}>
          <li className={styles["profile_sidebar-sub-menu__item"]}>
            <Link href="/profile/card/block">
              <a>Заблокировать карту</a>
            </Link>
          </li>
          <li className={styles["profile_sidebar-sub-menu__item"]}>
            <Link href="/profile/change-password">
              <a>Сменить пароль</a>
            </Link>
          </li>
          <li className={styles["profile_sidebar-sub-menu__item"]}>
            <Link href="/profile/card">
              <a>Список карт</a>
            </Link>
          </li>
        </ul>
      </aside>

      <span className={styles.profile_logo}>Бонусная программа</span>

      <Table shadow rounded striped>
        <tr>
          <td>№ карты:</td>
          <td>{user?.card.number}</td>
        </tr>
        <tr>
          <td>Количество покупок:</td>
          <td>{user?.quantity}</td>
        </tr>
        <tr>
          <td>Получено баллов:</td>
          <td>{user?.card.chargedBonus}</td>
        </tr>
        <tr>
          <td>Потрачено баллов:</td>
          <td>{user?.card.writeoffBonus}</td>
        </tr>
        <tr>
          <td>Общий баланс:</td>
          <td>{user?.balance}</td>
        </tr>
        <tr>
          <td>Активный баланс:</td>
          <td>{user?.activeBalance}</td>
        </tr>
      </Table>

      <article className={classNames(styles.profile_content, contentClassName)}>
        {title && <h4>{title}</h4>}
        {children}
      </article>
    </section>
  );
};

export default Profile;
