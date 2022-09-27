import { FC, ReactNode, useState } from "react";
import { Container, Row } from "react-bootstrap";
import Header from "./header";
import Footer from "./footer";
import bannerImage from "../../assets/images/banner.png";
import Image from "next/image";
import { NotificationContainer } from "react-notifications";
import Loyalty from "../loyalty";
import Auth from "../auth";
import {useAuth} from "../../hooks/useAuth";
import TopInfo from "./topInfo";

type Props = {
  children?: ReactNode;
  banner?: boolean;
};

const Layout: FC<Props> = ({ children, banner }) => {
    const {isAuth} = useAuth();
  const [showModal, setShowModal] = useState<boolean>(false);

  const handleClick = () => {
    !isAuth && setShowModal(true);
  };

  return (
    <>
      <TopInfo />

      <Header />

      <NotificationContainer />

      {banner && (
        <Container>
          <Row style={{ justifyContent: "center" }}>
            <Loyalty.Banner onClick={handleClick} />
          </Row>
        </Container>
      )}

      <Container as="main" className="my-5">
        {children}
      </Container>

      <Footer />

      <Auth
        show={showModal}
        type="register"
        onHide={() => setShowModal(false)}
      />
    </>
  );
};

export default Layout;
