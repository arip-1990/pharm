import { FC, ReactNode } from "react";
import styles from "./Order.module.scss";

type Props = {
  className?: string;
  children?: ReactNode;
};

const Order: FC<Props> = ({ className, children }) => {
  let classes = [styles.order];
  if (className) classes = classes.concat(className.split(" "));

  return <article className={classes.join(" ")}>{children}</article>;
};

export default Order;
