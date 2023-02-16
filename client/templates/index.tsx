import { FC, ReactNode, useState } from "react";
import Head from "next/head";
import { Container, Row } from "react-bootstrap";
import { NotificationContainer } from "react-notifications";

import Header from "./header";
import Footer from "./footer";
import Loyalty from "../components/loyalty";
import Auth from "../components/auth";
import TopInfo from "./topInfo";
import Banner from "../components/banner";
import { useAuth } from "../hooks/useAuth";

type Props = {
  title: string;
  description?: string;
  children?: ReactNode;
  banner?: boolean;
  type?: "main" | "loyalty";
};

const Layout: FC<Props> = ({
  title,
  description,
  children,
  banner,
  type = "main",
}) => {
  const { isAuth } = useAuth();
  const [showModal, setShowModal] = useState<boolean>(false);

  const handleClick = () => setShowModal(true);

  return (
    <>
      <Head>
        <title>{title}</title>
        <meta name="title" content={title} />
        <meta
          name="description"
          content={
            description ||
            "Добро пожаловать на наш сайт - сервис для покупки лекарств и товаров в собственной аптечной сети! Наши аптеки популярны, благодаря широкому ассортименту и высокой культуре обслуживания при доступных ценах. Гарантия качества и сервисное обслуживание – основные принципы нашей работы!"
          }
        />
      </Head>
      <TopInfo />

      <Header />

      <NotificationContainer />

      {banner && (
        <Container>
          <Row style={{ justifyContent: "center" }}>
            {type == "main" ? (
              <Banner />
            ) : (
              <Loyalty.Banner disabled={isAuth} onClick={handleClick} />
            )}
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