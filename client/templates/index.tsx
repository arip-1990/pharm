import { FC, ReactNode, useState, useEffect } from "react";
import { useRouter } from "next/router";
import Head from "next/head";
import {Col, Container, Row} from "react-bootstrap";
import { NotificationContainer } from "react-notifications";

import Header from "./header";
import Footer from "./footer";
import TopInfo from "./topInfo";
import Banner from "../components/banner";
import Loader from "../components/loader";

// import smal from './smal.jpg'
import smal1 from './small1.jpg'
import smal2 from './small2.jpg'

import new1 from './new2.jpg'
import new2 from './new1.jpg'

import big from './big.jpg'

import t11 from './t11.png'


import Image from "next/image";
import classes from './sliderStyle.module.css'


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
        <Container className='p-0'>
          <div className={classes.container}>
            <div className={classes.col9}>
              {/*<Image width={965} height={421} style={{borderRadius:'10px'}} src={big} />*/}
              <div className={classes.content}>
                <Banner />
              </div>
            </div>

            <div className={classes.col3}>
                <a className={classes.link}> <div className={classes.img} /> </a>
                <a className={classes.link} href={'https://makhachkala.hh.ru/employer/4291968 '}><div className={classes.img} /></a>
            </div>
          </div>
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
