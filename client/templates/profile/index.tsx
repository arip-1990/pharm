import { FC, MouseEvent, ReactNode, useEffect } from "react";
import classNames from "classnames";
import Link from "next/link";
import Image from "next/image";
import { useRouter } from "next/router";

import { Table } from "../../components/table";
import { useAuth } from "../../hooks/useAuth";

import styles from "./Profile.module.scss";

import loyaltyCard from "../../assets/images/loyalty-card.png";

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
            <a>
              <i className="icon-home" /> Главная
            </a>
          </Link>
          <Link href="/profile/cheque">
            <a>
              <i className="icon-cart" /> Покупки
            </a>
          </Link>
          <Link href="/profile/coupon">
            <a>
              <i className="icon-stack" /> Купоны
            </a>
          </Link>
          <Link href="/profile">
            <a>
              <i className="icon-file-text" /> Анкета
            </a>
          </Link>
          <a href="#" onClick={handleLogout}>
            <i className="icon-cross" /> Выход
          </a>
        </nav>

        <div className={styles["profile_sidebar-card"]}>
          <Image src={loyaltyCard} />
        </div>

        <ul
          className={classNames(
            styles["profile-sub-menu"],
            styles["profile-sub-menu__desktop"]
          )}
        >
          <li className={styles["profile-sub-menu_item"]}>
            <Link href="/profile/card/lock">
              <a>
                <i className="icon-lock" /> Заблокировать карту
              </a>
            </Link>
          </li>
          <li className={styles["profile-sub-menu_item"]}>
            <Link href="/profile/change-password">
              <a>
                <i className="icon-loop" /> Сменить пароль
              </a>
            </Link>
          </li>
          <li className={styles["profile-sub-menu_item"]}>
            <Link href="/profile/card">
              <a>
                <i className="icon-card" /> Список карт
              </a>
            </Link>
          </li>
        </ul>
      </aside>

      <span className={styles.profile_logo}>Бонусная программа</span>

      <div className={styles["profile_card-info"]}>
        <Table shadow rounded striped>
          <tr>
            <td>№ карты</td>
            <td>{user?.card.number}</td>
          </tr>
          <tr>
            <td>Количество покупок</td>
            <td>{user?.quantity}</td>
          </tr>
          <tr>
            <td>Получено баллов</td>
            <td>{user?.card.chargedBonus}</td>
          </tr>
          <tr>
            <td>Потрачено баллов</td>
            <td>{user?.card.writeoffBonus}</td>
          </tr>
          <tr>
            <td>Общий баланс</td>
            <td>{user?.balance}</td>
          </tr>
          <tr>
            <td>Активный баланс</td>
            <td>{user?.activeBalance}</td>
          </tr>
        </Table>
      </div>

      <article className={classNames(styles.profile_content, contentClassName)}>
        {title && <h5>{title}</h5>}
        {children}
      </article>

      <ul
        className={classNames(
          styles["profile-sub-menu"],
          styles["profile-sub-menu__mobile"]
        )}
      >
        <li className={styles["profile-sub-menu_item"]}>
          <Link href="/profile/card/lock">
            <a>
              <i className="icon-lock" /> Заблокировать карту
            </a>
          </Link>
        </li>
        <li className={styles["profile-sub-menu_item"]}>
          <Link href="/profile/change-password">
            <a>
              <i className="icon-loop" /> Сменить пароль
            </a>
          </Link>
        </li>
        <li className={styles["profile-sub-menu_item"]}>
          <Link href="/profile/card">
            <a>
              <i className="icon-card" /> Список карт
            </a>
          </Link>
        </li>
      </ul>
    </section>
  );
};

export default Profile;
