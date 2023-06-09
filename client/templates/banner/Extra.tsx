import React from "react";

import styles from "./sliderStyle.module.scss";

const Extra = () => {
  return (
    <div className={styles.col3}>
      <a className={styles.link}>
        {" "}
        <div
          className={styles.img}
          style={{
            backgroundImage: `url(https://w.forfun.com/fetch/94/94c56e15f13f1de4740a76742b0b594f.jpeg?h=900&r=0.5)`,
          }}
        />{" "}
      </a>
      <a
        className={styles.link}
        href={"https://makhachkala.hh.ru/employer/4291968 "}
      >
        <div className={styles.img} />
      </a>
    </div>
  );
};

export default Extra;
