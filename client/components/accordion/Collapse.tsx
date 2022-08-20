import { useContext, forwardRef, Children, HTMLAttributes } from "react";
import { AccordionContext, isAccordionItemSelected } from "./Context";
import classNames from "classnames";
import styles from "./Accordion.module.scss";

export interface Props extends HTMLAttributes<HTMLElement> {
  eventKey: string;
}

const AccordionCollapse = forwardRef<HTMLDivElement, Props>(
  ({ className, children, eventKey, ...props }, ref) => {
    const { activeEventKey } = useContext(AccordionContext);

    console.log(activeEventKey);
    console.log(eventKey);

    return (
      <div
        ref={ref}
        {...props}
        aria-expanded={eventKey === activeEventKey}
        className={classNames(
          styles["accordion-item_body"],
          className,
          isAccordionItemSelected(activeEventKey, eventKey)
            ? styles.animateIn
            : styles.animateOut,
          {
            [styles.collapsed]: !isAccordionItemSelected(
              activeEventKey,
              eventKey
            ),
          }
        )}
      >
        {Children.only(children)}
      </div>
    );
  }
) as any;

export default AccordionCollapse;
