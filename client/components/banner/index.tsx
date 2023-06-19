import { FC, useEffect, useState } from "react";
import { Col, Container, Row } from "react-bootstrap";
import { Swiper, SwiperSlide } from "swiper/react";
import { Autoplay, Navigation, Pagination } from "swiper";

import { useFetchBannersQuery } from "../../lib/bannerService";
import { Extra } from "./Extra";

import styles from "./Banner.module.scss";

const Banner: FC = () => {
  const [isMobile, setIsMobile] = useState<boolean>(false);
  const [existsExtra, setExistsExtra] = useState<boolean>(false);
  const { data, isFetching } = useFetchBannersQuery();

  useEffect(() => {
    const changeMobileState = () => {
      if (window.matchMedia("(min-width: 768px)").matches) setIsMobile(false);
      else setIsMobile(true);
    };

    window.addEventListener("resize", changeMobileState);

    return () => window.removeEventListener("resize", changeMobileState);
  }, []);

  useEffect(() => {
    data?.forEach((item) => {
      if (item.type === "extra") {
        setExistsExtra(true);
        return;
      }
    });
  }, [data]);


      {data?.filter(item => item.type !== 'main').map((item) => (
          <SwiperSlide key={item.id} >
          <div
            className={styles.banner}
            style={{
              backgroundImage:
                isMobile && item.picture.mobile
                  ? `url(${item.picture.mobile})`
                  : `url(${item.picture.main})`,
            }}
          />
          </SwiperSlide>
      ))}


  return (
    <Container className="p-0">
      <Row style={{ rowGap: "1rem" }}>
        <Col xs={12} lg={existsExtra ? 9 : 12}>
          <Swiper
            autoplay={{ delay: 10000, disableOnInteraction: false }}
            spaceBetween={16}
            navigation={true}
            pagination={true}
            modules={[Autoplay, Navigation, Pagination]}
          >
            {data
              ?.filter((item) => ["main", "all"].includes(item.type))
              .map((item) => (
                <SwiperSlide key={item.id}>
                  <div
                    className={styles.banner}
                    style={{
                      backgroundImage:
                        isMobile && item.picture.mobile
                          ? `url(${item.picture.mobile})`
                          : `url(${item.picture.main})`,
                    }}
                  />
                </SwiperSlide>
              ))}
          </Swiper>
        </Col>
        {existsExtra ? (
          <Col xs={12} lg={3}>
            <Extra data={data?.filter((item) => item.type === "extra")} />
          </Col>
        ) : null}
      </Row>
    </Container>
  );
};

export default Banner;





