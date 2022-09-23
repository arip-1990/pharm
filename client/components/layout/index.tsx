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

type Props = {
  children?: ReactNode;
  banner?: boolean | ReactNode;
};

const Layout: FC<Props> = ({ children, banner }) => {
    const {isAuth} = useAuth();
  const [showModal, setShowModal] = useState<boolean>(false);

  const handleClick = () => {
    !isAuth && setShowModal(true);
  };

  return (
    <>
      <Header />

      <NotificationContainer />

      {banner ? (
        // <Container fluid={banner === true}>
        //   <Row style={{ justifyContent: "center" }}>
        //     {typeof banner === "boolean" ? <Image src={bannerImage} /> : banner}
        //   </Row>
        // </Container>
        <Container>
          <Row style={{ justifyContent: "center" }}>
            <Loyalty.Banner onClick={handleClick} />
          </Row>
        </Container>
      ) : null}

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
