import { FC, ReactNode } from "react";
import { Container, Row } from "react-bootstrap";
import Header from "./header";
import Footer from "./footer";
import bannerImage from "../../assets/images/banner.png";
import Image from "next/image";

type Props = {
  children?: ReactNode;
  banner?: boolean;
};

const Layout: FC<Props> = ({ children, banner }) => {
  return (
    <>
      <Header />

      {banner ? (
        <Container fluid>
          <Row>
            <Image src={bannerImage} />
          </Row>
        </Container>
      ) : null}

      <Container as="main" className="my-3">
        {children}
      </Container>

      <Footer />
    </>
  );
};

export default Layout;
