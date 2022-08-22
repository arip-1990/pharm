import { useRouter } from "next/router";
import { FC, MouseEvent, ReactNode } from "react";
import { useAuth } from "../../hooks/useAuth";
import styles from "./Profile.module.scss";

type Props = {
  title?: string;
  className?: string;
  children?: ReactNode;
};

const Profile: FC<Props> = ({ title, className, children }) => {
  const { user, logout } = useAuth();
  const router = useRouter();
  let classes = [styles.profile];
  if (className) classes = classes.concat(className.split(" "));

  const handleLogout = async (e: MouseEvent) => {
    e.preventDefault();
    try {
      await logout();
      router.push("/");
    } catch (error) {
      console.log(error);
    }
  };

  return (
    <section className={classes.join(" ")}>
      <div className="row">
        <div className="col-3">
          <div className={styles["profile_logo"]}>
            <div className="fa-regular fa-gift" />
            <div>Бонусная программа</div>
          </div>
        </div>
        <div className="col-9">
          <nav className={styles["profile_nav"]}>
            <a href="#">Главная</a>
            <a href="#">Покупки</a>
            <a href="#">Купоны</a>
            <a href="#">Анкета</a>
            <a href="#" onClick={handleLogout}>
              Выход
            </a>
          </nav>
          {title && <h4 className="m-0">{title}</h4>}
        </div>
      </div>

      <div className="row">
        <aside className={styles["profile-sidebar"] + " col-3"}>
          <div className={styles["profile-sidebar_info"]}>
            <ul>
              <li>
                <b>№ карты:</b>
              </li>
              <li>
                <b>Количество покупок:</b>
              </li>
              <li>
                <b>Получено баллов:</b>
              </li>
              <li>
                <b>Потрачено баллов:</b>
              </li>
              <li>
                <b>Общий баланс:</b>
              </li>
              <li>
                <b>Активный баланс:</b>
              </li>
            </ul>
          </div>
          <ul>
            <li>
              <a href="#">Заблокировать карту</a>
            </li>
            <li>
              <a href="#">Сменить пароль</a>
            </li>
            <li>
              <a href="#">Список карт</a>
            </li>
          </ul>
        </aside>
        <div className="col-9">
          <article className={styles["profile-content"]}>{children}</article>
        </div>
      </div>
    </section>
  );
};

export default Profile;
