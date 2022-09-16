import { FC } from "react";

import styles from "./Loader.module.scss";

const Spinner: FC = () => {
  return (
    <div className={styles.loader_spinner}>
      <div />
      <div />
      <div />
      <div />
      <div />
      <div />
      <div />
      <div />
      <div />
      <div />
      <div />
      <div />
    </div>
  );
};

export default Spinner;
