import React, {
  cloneElement,
  ComponentClass,
  forwardRef,
  ReactElement,
  ReactNode,
  Ref,
  useCallback,
  useRef,
} from "react";
import ReactDOM from "react-dom";
import Transition, {
  TransitionProps,
  TransitionStatus,
} from "react-transition-group/Transition";
import useMergedRefs from "@restart/hooks/useMergedRefs";

export type Props = TransitionProps & {
  childRef?: Ref<unknown>;
  children:
    | ReactElement
    | ((status: TransitionStatus, props: Record<string, unknown>) => ReactNode);
};

const safeFindDOMNode = (
  componentOrElement: React.ComponentClass | Element | null | undefined
) => {
  if (componentOrElement && "setState" in componentOrElement) {
    return ReactDOM.findDOMNode(componentOrElement);
  }
  return (componentOrElement ?? null) as Element | Text | null;
};

// Normalizes Transition callbacks when nodeRef is used.
const TransitionWrapper = forwardRef<Transition<any>, Props>(
  (
    {
      onEnter,
      onEntering,
      onEntered,
      onExit,
      onExiting,
      onExited,
      addEndListener,
      children,
      childRef,
      ...props
    },
    ref
  ) => {
    const nodeRef = useRef<HTMLElement>(null);
    const mergedRef = useMergedRefs(nodeRef, childRef);

    const attachRef = (r: ComponentClass | Element | null | undefined) => {
      mergedRef(safeFindDOMNode(r));
    };

    const normalize = (callback?: (node: HTMLElement, param: any) => void) => (
      param: any
    ) => {
      if (callback && nodeRef.current) {
        callback(nodeRef.current, param);
      }
    };

    /* eslint-disable react-hooks/exhaustive-deps */
    const handleEnter = useCallback(onEnter, [onEnter]);
    const handleEntering = useCallback(normalize(onEntering), [onEntering]);
    const handleEntered = useCallback(normalize(onEntered), [onEntered]);
    const handleExit = useCallback(normalize(onExit), [onExit]);
    const handleExiting = useCallback(normalize(onExiting), [onExiting]);
    const handleExited = useCallback(normalize(onExited), [onExited]);
    const handleAddEndListener = useCallback(normalize(addEndListener), [
      addEndListener,
    ]);

    return (
      <Transition
        ref={ref}
        {...props}
        onEnter={handleEnter}
        onEntered={handleEntered}
        onEntering={handleEntering}
        onExit={handleExit}
        onExited={handleExited}
        onExiting={handleExiting}
        addEndListener={handleAddEndListener}
        nodeRef={nodeRef}
      >
        {typeof children === "function"
          ? (status: TransitionStatus, innerProps: Record<string, unknown>) =>
              children(status, { ...innerProps, ref: attachRef })
          : cloneElement(children as ReactElement, { ref: attachRef })}
      </Transition>
    );
  }
);

export default TransitionWrapper;
