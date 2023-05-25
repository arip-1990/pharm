import { forwardRef, HTMLAttributes, useMemo } from "react";

import { AccordionItemContext, AccordionItemContextValue } from "./Context";

import styles from "./Accordion.module.scss";

interface Props extends HTMLAttributes<HTMLElement> {
  as?: any;
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
