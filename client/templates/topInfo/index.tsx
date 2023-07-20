import { FC, MouseEvent, useState } from "react";
import { Container } from "react-bootstrap";
import Link from "next/link";

import { SetCity } from "./SetCity";
import Auth from "../../components/auth";
import DeliverySale from "./DeliverySale";
import { useAuth } from "../../hooks/useAuth";
import { useCookie } from "../../hooks/useCookie";
import { useFetchCitiesQuery } from "../../lib/cityService";

import styles from "./TopInfo.module.scss";

const TopInfo: FC = () => {
  const { isAuth, user } = useAuth();
  const [city, setCookie] = useCookie("city");
  const { data: cities } = useFetchCitiesQuery();
  const [showModal, setShowModal] = useState<boolean>(false);

  const handleSignIn = (e: MouseEvent) => {
    e.preventDefault();
    setShowModal(true);
  };

  return (
    <Container className="my-3">
      <div className={styles.topInfo}>
        {cities && <SetCity city={city || cities[0].name} cities={cities} setCity={setCookie} />}

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
