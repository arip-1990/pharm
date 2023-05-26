import { FC, ReactNode, useState, useEffect } from "react";
import { useRouter } from "next/router";
import Head from "next/head";
import { Container, Row } from "react-bootstrap";
import { NotificationContainer } from "react-notifications";

import Header from "./header";
import Footer from "./footer";
import TopInfo from "./topInfo";
import Banner from "../components/banner";
import Loader from "../components/loader";

type Props = {
  title: string;
  description?: string;
  children?: ReactNode;
  banner?: boolean;
  loading?: boolean;
};

const Layout: FC<Props> = ({
  title,
  description,
  children,
  banner,
  loading,
}) => {
  const router = useRouter();
  const [isLoading, setIsLoading] = useState<boolean>(loading || false);

  useEffect(() => {
    const handleStart = () => {
      setIsLoading(true);
      if (document.body.offsetHeight > window.innerHeight)
        document.body.style.paddingRight = "15px";
      document.body.style.overflow = "hidden";
    };
    const handleComplete = () => {
      setIsLoading(false);
      document.body.removeAttribute("style");
    };

    router.events.on("routeChangeStart", handleStart);
    router.events.on("routeChangeComplete", handleComplete);
    router.events.on("routeChangeError", handleComplete);

    return () => {
      router.events.off("routeChangeStart", handleStart);
      router.events.off("routeChangeComplete", handleComplete);
      router.events.off("routeChangeError", handleComplete);
    };
  }, []);

  return (
    <>
      {(isLoading || loading) && <Loader />}
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

      {(isLoading || loading) && <Loader />}

      <TopInfo />

      <Header />

      <NotificationContainer />

      {banner && (
        <Container>
          <Row style={{ justifyContent: "center" }}>
            <Banner />
          </Row>
        </Container>
      )}

      <Container as="main" className="my-5">
        {children}
      </Container>

      <Footer />
    </>
  );
};

export default Layout;
