import { forwardRef, HTMLAttributes, useContext } from "react";

import { AccordionItemContext } from "./Context";
import AccordionCollapse from "./Collapse";

interface Props extends HTMLAttributes<HTMLElement> {
  as?: any;
}

const Body = forwardRef<HTMLElement, Props>(
  ({ as: Component = "div", className, ...props }, ref) => {
    const { eventKey } = useContext(AccordionItemContext);

    return (
      <AccordionCollapse eventKey={eventKey}>
        <Component ref={ref} {...props} className={className} />
      </AccordionCollapse>
    );
  }
);

export default Body;
