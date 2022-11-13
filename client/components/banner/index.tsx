import { FC } from "react";
import { Swiper, SwiperSlide } from "swiper/react";
import { Autoplay, Pagination } from "swiper";

import styles from "./Banner.module.scss";

const Banner: FC = () => {
    return (
        <Swiper autoplay={{delay: 10000, disableOnInteraction: false}} autoHeight={true} spaceBetween={16} pagination={true} modules={[Autoplay, Pagination]}>
            <SwiperSlide><div className={`${styles.banner} ${styles.banner_1}`} /></SwiperSlide>
            <SwiperSlide><div className={`${styles.banner} ${styles.banner_2}`} /></SwiperSlide>
        </Swiper>
    );
}

export default Banner;
