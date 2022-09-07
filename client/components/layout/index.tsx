import { FC, ReactNode } from "react";
import { Container, Row } from "react-bootstrap";
import Header from "./header";
import Footer from "./footer";
import bannerImage from "../../assets/images/banner.png";
import Image, { StaticImageData } from "next/image";
import { NotificationContainer } from "react-notifications";

type Props = {
  children?: ReactNode;
  banner?: boolean | ReactNode;
};

const Layout: FC<Props> = ({ children, banner }) => {
  return (
    <>
      <Header />

      <NotificationContainer />

      {banner ? (
        <Container fluid={banner === true}>
          <Row style={{ justifyContent: "center" }}>
            {typeof banner === "boolean" ? <Image src={bannerImage} /> : banner}
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
