import { FC, useCallback, useState } from "react";
import { Modal } from "react-bootstrap";

import styles from "./Carousel.module.scss";

interface Props {
  show: boolean;
  onHide: () => void;
  data: { id: number; url: string }[];
}

const Carousel: FC<Props> = ({ show, onHide, data }) => {
  const [image, setImage] = useState<{
    prev: number;
    main: number;
    next: number;
  }>({ prev: data.length - 1, main: 0, next: 1 });

  const scrollRight = useCallback(() => {
    setImage((item) => ({
      prev: item.main,
      main: item.next,
      next: item.next < data.length - 1 ? item.next + 1 : 0,
    }));
  }, [data]);

  const scrollLeft = useCallback(() => {
    setImage((item) => ({
      prev: item.prev !== 0 ? item.prev - 1 : data.length - 1,
      main: item.prev,
      next: item.main,
    }));
  }, [data]);

  if (data.length > 1) {
    return (
      <Modal
        dialogClassName={styles.container}
        contentClassName={styles.carousel}
        show={show}
        onHide={onHide}
        centered
      >
        <div
          className={styles.carousel_leftView}
          style={{ backgroundImage: `url(${data[image.prev].url})` }}
          onClick={scrollLeft}
        />
        <div
          className={styles.carousel_mainView}
          style={{ backgroundImage: `url(${data[image.main].url})` }}
        />
        <div
          className={styles.carousel_rightView}
          style={{ backgroundImage: `url(${data[image.next].url})` }}
          onClick={scrollRight}
        />
      </Modal>
    );
  }

  return (
    <Modal
      contentClassName={styles.carousel}
      show={show}
      onHide={onHide}
      centered
    >
      <div
        className={styles.carousel_mainView}
        style={{ backgroundImage: `url(${data[image.main].url})` }}
      />
    </Modal>
  );
};

export { Carousel };
