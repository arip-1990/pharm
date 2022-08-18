import classNames from "classnames";
import {
  ElementType,
  EventHandler,
  forwardRef,
  HTMLAttributes,
  ReactNode,
  SyntheticEvent,
  useContext,
} from "react";
import {
  AccordionContext,
  AccordionEventKey,
  AccordionItemContext,
  isAccordionItemSelected,
} from "./Context";
import styles from "./Accordion.module.scss";

type CustomEventHandler = EventHandler<SyntheticEvent>;

interface Props extends HTMLAttributes<HTMLElement> {
  as?: ElementType;
  children?: ReactNode;
}

const useAccordionButton = (
  eventKey: string,
  onClick?: CustomEventHandler
): CustomEventHandler => {
  const { activeEventKey, onSelect } = useContext(AccordionContext);

  return (e) => {
    let eventKeyPassed: AccordionEventKey =
      eventKey === activeEventKey ? null : eventKey;
    onSelect?.(eventKeyPassed, e);
    onClick?.(e);
  };
};

const Header = forwardRef<HTMLElement, Props>(
  ({ as: Component = "div", className, onClick, ...props }, ref) => {
    const { activeEventKey } = useContext(AccordionContext);
    const { eventKey } = useContext(AccordionItemContext);
    const accordionOnClick = useAccordionButton(eventKey, onClick);

    return (
      <Component
        ref={ref}
        onClick={accordionOnClick}
        {...props}
        aria-expanded={eventKey === activeEventKey}
        className={classNames(
          styles["accordion-item_header"],
          className,
          !isAccordionItemSelected(activeEventKey, eventKey) && styles.collapsed
        )}
      />
    );
  }
);

export default Header;
