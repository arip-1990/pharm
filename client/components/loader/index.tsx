import { FC } from "react";

import Spinner from "./Spinner";

import styles from "./Loader.module.scss";

const Loader: FC = () => {
  return (
    <div className={styles.loader}>
      <Spinner />
    </div>
  );
};

export default Loader;
