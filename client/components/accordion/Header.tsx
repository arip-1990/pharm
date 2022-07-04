import { ElementType, FC, HTMLAttributes, ReactNode } from "react";
import styles from "./Accordion.module.scss";

interface Props extends HTMLAttributes<HTMLElement> {
  as?: ElementType;
  children?: ReactNode;
}

const Header: FC<Props> = ({
  as: Tag = "div",
  className,
  children,
  ...props
}) => {
  let classes = [styles.accordionItem_header];
  if (className) classes = classes.concat(className.split(" "));

  return (
    <Tag className={classes.join(" ")} {...props}>
      {children}
    </Tag>
  );
};

export default Header;
