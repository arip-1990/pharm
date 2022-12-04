import classNames from "classnames";
import Link from "next/link";
import { useRouter } from "next/router";
import { FC, MouseEvent, ReactNode, useEffect } from "react";
import { useAuth } from "../../hooks/useAuth";
import styles from "./Profile.module.scss";

type Props = {
  title?: string;
  className?: string;
  children?: ReactNode;
  contentClassName?: string;
};

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
      // router.push("/");
    } catch (error) {
      console.log(error);
    }
  };

  return (
    <section className={classes.join(" ")}>
      <div className="row">
        <div className="col-3">
          <div className={styles["profile_logo"]}>
            <div className="icon-gift" />
            <div>Бонусная программа</div>
          </div>
        </div>
        <div
          className="col-9"
          style={{ display: "flex", flexDirection: "column" }}
        >
          <nav className={styles["profile_nav"]}>
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
          {title && <h4 className="mb-0 mt-auto">{title}</h4>}
        </div>
      </div>

      <div className="row">
        <aside className={styles["profile-sidebar"] + " col-3"}>
          <div className={styles["profile-sidebar_info"]}>
            <ul>
              <li style={{ fontSize: "1.2rem" }}>
                <span>№ карты:</span> {user?.card.number}
              </li>
              <li style={{ fontSize: "1.2rem" }}>
                <span>Количество покупок:</span> {user?.quantity}
              </li>
              <li style={{ fontSize: "1.2rem" }}>
                <span>Получено баллов:</span> {user?.card.chargedBonus}
              </li>
              <li style={{ fontSize: "1.2rem" }}>
                <span>Потрачено баллов:</span> {user?.card.writeoffBonus}
              </li>
              <li style={{ fontSize: "1.2rem" }}>
                <span>Общий баланс:</span> {user?.balance}
              </li>
              <li style={{ fontSize: "1.2rem" }}>
                <span>Активный баланс:</span> {user?.activeBalance}
              </li>
            </ul>
          </div>
          <ul>
            <li style={{ fontSize: "1.1rem" }}>
              <Link href="/profile/card/block">
                <a>Заблокировать карту</a>
              </Link>
            </li>
            <li style={{ fontSize: "1.1rem" }}>
              <Link href="/profile/change-password">
                <a>Сменить пароль</a>
              </Link>
            </li>
            <li style={{ fontSize: "1.1rem" }}>
              <Link href="/profile/card">
                <a>Список карт</a>
              </Link>
            </li>
          </ul>
        </aside>

        <div className="col-9">
          <article
            className={classNames(styles["profile-content"], contentClassName)}
          >
            {children}
          </article>
        </div>
      </div>
    </section>
  );
};

export default Profile;
