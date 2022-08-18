import { ElementType, forwardRef, HTMLAttributes, useMemo } from "react";
import styles from "./Accordion.module.scss";
import { AccordionItemContext, AccordionItemContextValue } from "./Context";

interface Props extends HTMLAttributes<HTMLElement> {
  as?: ElementType;
  eventKey: string;
}

const Item = forwardRef<HTMLElement, Props>(
  ({ as: Component = "div", className, eventKey, ...props }, ref) => {
    let classes = [styles["accordion-item"]];
    if (className) classes = classes.concat(className.split(" "));

    const contextValue = useMemo<AccordionItemContextValue>(
      () => ({ eventKey }),
      [eventKey]
    );

    return (
      <AccordionItemContext.Provider value={contextValue}>
        <Component ref={ref} {...props} className={classes.join(" ")} />
      </AccordionItemContext.Provider>
    );
  }
);

export default Item;
