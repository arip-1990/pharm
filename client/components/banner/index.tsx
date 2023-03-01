import { FC } from "react";
import { Swiper, SwiperSlide } from "swiper/react";
import { Autoplay, Navigation, Pagination } from "swiper";

import styles from "./Banner.module.scss";

const Banner: FC = () => {
  return (
    <Swiper
      style={{ padding: 0 }}
      autoplay={{ delay: 10000, disableOnInteraction: false }}
      autoHeight={true}
      spaceBetween={16}
      navigation={true}
      pagination={true}
      modules={[Autoplay, Navigation, Pagination]}
    >
      <SwiperSlide>
        <div className={`${styles.banner} ${styles.banner_1}`} />
      </SwiperSlide>
      <SwiperSlide>
        <div className={`${styles.banner} ${styles.banner_2}`} />
      </SwiperSlide>
      <SwiperSlide>
        <div className={`${styles.banner} ${styles.banner_3}`} />
      </SwiperSlide>
    </Swiper>
  );
};

export default Banner;
