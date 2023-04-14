import { FC, MouseEvent, useCallback, useState } from "react";

import styles from "./Carousel.module.scss";

interface Props {
  data: { id: number; url: string }[];
}

const Carousel: FC<Props> = ({ data }) => {
  const [image, setImage] = useState<{
    main: number;
    prev: number;
    next: number;
  }>({ main: 0, next: 1, prev: data.length - 1 });

  const scrollRight = useCallback(
    (event: MouseEvent<HTMLDivElement>) => {
      event.preventDefault();
      let tmp = { prev: image.main, main: image.next, next: 0 };
      if (image.next < data.length - 1) tmp.next++;

      setImage(tmp);
    },
    [data]
  );

  const scrollLeft = useCallback(
    (event: MouseEvent<HTMLDivElement>) => {
      event.preventDefault();
      let tmp = { next: image.main, main: image.prev, prev: data.length - 1 };
      if (image.prev !== 0) tmp.prev--;

      setImage(tmp);
    },
    [data]
  );

  return (
    <div className={styles.carousel}>
      <div
        className={styles.carousel_leftView}
        style={{ background: `url(${data[image.prev].url})` }}
        onClick={scrollLeft}
      />
      <div
        className={styles.carousel_mainView}
        style={{ background: `url(${data[image.main].url})` }}
      />
      <div
        className={styles.carousel_rightView}
        style={{ background: `url(${data[image.next].url})` }}
        onClick={scrollRight}
      />
    </div>
  );
};

export { Carousel };
