import { FC, useEffect, useState } from "react";
import { Swiper, SwiperSlide } from "swiper/react";
import { Autoplay, Navigation, Pagination } from "swiper";
import { useFetchBannersQuery } from "../../lib/bannerService";
import styles from "./Banner.module.scss";


const Banner: FC = () => {
  const [isMobile, setIsMobile] = useState<boolean>(false);
  const { data, isFetching } = useFetchBannersQuery();

  console.log(data)

  useEffect(() => {
    const changeMobileState = () => {
      if (window.matchMedia("(min-width: 768px)").matches) setIsMobile(false);
      else setIsMobile(true);
    };

    window.addEventListener("resize", changeMobileState);

    return () => window.removeEventListener("resize", changeMobileState);
  }, []);

  return (
    <Swiper
      style={{ padding: 0, height:'100%'}}
      autoplay={{ delay: 10000, disableOnInteraction: false }}
      autoHeight={true}
      spaceBetween={16}
      navigation={true}
      pagination={true}
      modules={[Autoplay, Navigation, Pagination]}
    >

      {data?.filter(item => item.type !== 2).map((item) => (
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
    </Swiper>
  );
};


export default Banner;
