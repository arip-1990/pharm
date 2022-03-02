import { ReactNode } from 'react';
import Head from 'next/head';
import { Container, Row } from 'react-bootstrap';
import Header from './header';
import Footer from './footer';
import React from 'react';

interface PropsType {
    children?: ReactNode;
    banner?: boolean;
    title?: string;
}

export default ({ children, title, banner }: PropsType) => {
    return (
        <>
            <Head>
                <meta charSet='utf-8' />
                <meta name="viewport" content="width=device-width, initial-scale=1" />

                <title>{title || 'Pharm'}</title>
            </Head>

            <Header />

            {banner ? <Container fluid>
                <Row><img src="/images/banner.jpg" alt="banner" /></Row>
            </Container> : null}

            <Container as='main' className='my-3'>{children}</Container>

            <Footer />
        </>
    );
  }
