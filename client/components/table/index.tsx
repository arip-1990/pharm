import classNames from "classnames";
import { FC } from "react";

import styles from "./Table.module.scss";

interface Props {
  shadow?: boolean;
  rounded?: boolean;
  striped?: boolean;
}

const Table: FC<Props> = ({ children, shadow, rounded, striped }) => {
  return (
    <table
      className={classNames(styles.table, {
        [styles.table__shadow]: shadow,
        [styles.table__rounded]: rounded,
        [styles.table__striped]: striped,
      })}
    >
      <tbody>{children}</tbody>
    </table>
  );
};

export { Table };
