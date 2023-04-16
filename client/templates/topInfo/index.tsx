import { FC, MouseEvent, useState } from "react";
import { SetCity } from "./SetCity";
import { Container } from "react-bootstrap";
import Link from "next/link";
import Auth from "../../components/auth";
import DeliverySale from "./DeliverySale";
import { useAuth } from "../../hooks/useAuth";
import { useCookie } from "../../hooks/useCookie";

import styles from "./TopInfo.module.scss";

const TopInfo: FC = () => {
  const { isAuth, user } = useAuth();
  const [city, setCookie, removeCookie] = useCookie("city");
  const [showModal, setShowModal] = useState<boolean>(false);

  const handleSignIn = (e: MouseEvent) => {
    e.preventDefault();
    setShowModal(true);
  };

  const setCity = (city: string) => {
    removeCookie();
    setCookie(city);
  }

  return (
    <Container className="my-3">
      <div className={styles.topInfo}>
        <SetCity city={city} setCity={setCity} />

        {city?.toLowerCase().includes('махачкала') && <DeliverySale />}

        <div className="auth text-end">
          <span className="phone">+7 (8722) 606-366</span>
          <span className="d-inline-block">
            {isAuth ? (
              <Link href="/profile">
                <a>{user?.firstName + (user?.lastName ? ( ' ' + user.lastName.charAt(0)) : '')}</a>
              </Link>
            ) : (
              <a href="#" onClick={handleSignIn}>
                Войти
              </a>
            )}
          </span>
        </div>

        <Auth show={showModal} onHide={() => setShowModal(false)} />
      </div>
    </Container>
  );
};

export default TopInfo;
