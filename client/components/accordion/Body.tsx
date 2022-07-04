import { FC, HTMLAttributes, ReactNode } from "react";
import styles from "./Accordion.module.scss";

interface Props extends HTMLAttributes<HTMLElement> {
  children?: ReactNode;
}

const Body: FC<Props> = ({ className, children }, ...props) => {
  let classes = [styles.accordionItem_body, styles.collapsed];
  if (className) classes = classes.concat(className.split(" "));

  return (
    <div className={classes.join(" ")} {...props}>
      {children}
    </div>
  );
};

export default Body;
