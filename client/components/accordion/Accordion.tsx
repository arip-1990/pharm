import { FC, MouseEvent, ReactNode, useEffect, useRef } from "react";
import styles from "./Accordion.module.scss";

type Props = {
  children?: ReactNode;
};

const Accordion: FC<Props> = ({ children }) => {
  const accordionRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    if (accordionRef.current) {
      accordionRef.current.addEventListener("click", handleCollapse);
    }

    return () =>
      accordionRef.current?.removeEventListener("click", handleCollapse);
  }, []);

  const toggleClass = (element: HTMLElement | Element, className: string) => {
    element.classList.contains(className)
      ? element.classList.remove(className)
      : element.classList.add(className);
  };

  const handleCollapse = (e: MouseEvent<HTMLDivElement>) => {
    const target = e.target as HTMLDivElement;
    if (target.classList.contains(styles.accordionItem_header)) {
      e.stopPropagation();
      e.preventDefault();

      const content = target
        .closest("." + styles.accordionItem)
        .querySelector("." + styles.accordionItem_body);
      toggleClass(target, styles.active);
      if (content.classList.contains(styles.collapsed)) {
        if (content.classList.contains(styles.animateOut)) {
          content.classList.remove(styles.animateOut);
        }
        content.classList.add(styles.animateIn);
      } else {
        content.classList.remove(styles.animateIn);
        content.classList.add(styles.animateOut);
      }
      toggleClass(content, styles.collapsed);
    }
  };

  return (
    <div ref={accordionRef} className={styles.accordion}>
      {children}
    </div>
  );
};

export default Accordion;
