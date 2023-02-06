import { FC } from "react";

import styles from "./Table.module.scss";

const Table: FC = ({ children }) => {
  return (
    <table className={styles.table}>
      <tbody>{children}</tbody>
    </table>
  );
};

export { Table };
