import { FC, HTMLAttributes, ReactNode } from "react";
import styles from "./Accordion.module.scss";

interface Props extends HTMLAttributes<HTMLElement> {
  children?: ReactNode;
}

const Item: FC<Props> = ({ children, className, ...props }) => {
  let classes = [styles.accordionItem];
  if (className) classes = classes.concat(className.split(" "));

  return (
    <div className={classes.join(" ")} {...props}>
      {children}
    </div>
  );
};

export default Item;
