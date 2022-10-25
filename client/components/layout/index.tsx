import { FC, ReactNode, useState } from "react";
import { Container, Row } from "react-bootstrap";
import Header from "./header";
import Footer from "./footer";
import { NotificationContainer } from "react-notifications";
import Loyalty from "../loyalty";
import Auth from "../auth";
import {useAuth} from "../../hooks/useAuth";
import TopInfo from "./topInfo";
import Banner from "../banner";

type Props = {
  children?: ReactNode;
  banner?: boolean;
  type?: 'main' | 'loyalty';
};

const Layout: FC<Props> = ({ children, banner, type = 'main' }) => {
    const {isAuth} = useAuth();
  const [showModal, setShowModal] = useState<boolean>(false);

  const handleClick = () => {
    setShowModal(true);
  };

  return (
    <>
      <TopInfo />

      <Header />

      <NotificationContainer />

      {banner && (
        <Container>
          <Row style={{ justifyContent: "center" }}>
            {type == 'main' ? <Banner /> : <Loyalty.Banner disabled={isAuth} onClick={handleClick} />}
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
