import { HTMLAttributes, forwardRef, useMemo } from "react";
import { useUncontrolled } from "uncontrollable";

import {
  AccordionContext,
  AccordionEventKey,
  AccordionSelectCallback,
} from "./Context";

import styles from "./Accordion.module.scss";

interface Props extends Omit<HTMLAttributes<HTMLElement>, "onSelect"> {
  as?: any;
  activeKey?: AccordionEventKey;
  defaultActiveKey?: AccordionEventKey;
  onSelect?: AccordionSelectCallback;
}

const Accordion = forwardRef<HTMLElement, Props>((props, ref) => {
  const {
    as: Component = "div",
    activeKey,
    className,
    onSelect,
    ...controlledProps
  } = useUncontrolled(props, { activeKey: "onSelect" });
  let classes = [styles.accordion];
  if (className) classes = classes.concat(className.split(" "));

  const contextValue = useMemo(
    () => ({ activeEventKey: activeKey, onSelect }),
    [activeKey, onSelect]
  );

  return (
    <AccordionContext.Provider value={contextValue}>
      <Component ref={ref} {...controlledProps} className={classes.join(" ")} />
    </AccordionContext.Provider>
  );
});

export default Accordion;
