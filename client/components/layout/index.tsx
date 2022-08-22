import { FC, ReactNode } from "react";
import { Container, Row } from "react-bootstrap";
import Header from "./header";
import Footer from "./footer";
import bannerImage from "../../assets/images/banner.png";
import Image from "next/image";
import { useAlert } from "../../hooks/useAlert";

type Props = {
  children?: ReactNode;
  banner?: boolean;
};

const Layout: FC<Props> = ({ children, banner }) => {
  const { alerts } = useAlert();

  return (
    <>
      <Header />

      <Container>
        {alerts.map((alert) => (
          <div className={`alert alert-${alert.type}`} role="alert">
            {alert.message}
          </div>
        ))}
      </Container>

      {banner ? (
        <Container fluid>
          <Row>
            <Image src={bannerImage} />
          </Row>
        </Container>
      ) : null}

      <Container as="main" className="my-5">
        {children}
      </Container>

      <Footer />
    </>
  );
};

export default Layout;
