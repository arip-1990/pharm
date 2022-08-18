import classNames from "classnames";
import css from "dom-helpers/css";
import {
  cloneElement,
  forwardRef,
  HTMLAttributes,
  ReactElement,
  useMemo,
} from "react";
import Transition, {
  TransitionStatus,
  ENTERED,
  ENTERING,
  EXITED,
  EXITING,
} from "react-transition-group/Transition";
import { TransitionCallbacks } from "@restart/ui/types";
import transitionEndListener from "../transition/transitionEndListener";
import TransitionWrapper from "../transition/TransitionWrapper";
import { useMountedState } from "react-use";

const triggerBrowserReflow = (node: HTMLElement): void => {
  node.offsetHeight;
};

const createChainedFunction = (...funcs) =>
  funcs
    .filter((f) => f != null)
    .reduce((acc, f) => {
      if (typeof f !== "function") {
        throw new Error(
          "Invalid Argument Type, must only provide functions, undefined, or null."
        );
      }

      if (acc === null) return f;

      return function chainedFunction(...args) {
        console.log(args);
        acc.apply(this, args);
        f.apply(this, args);
      };
    }, null);

type Dimension = "height" | "width";

export interface CollapseProps
  extends TransitionCallbacks,
    Pick<HTMLAttributes<HTMLElement>, "role"> {
  className?: string;
  in?: boolean;
  mountOnEnter?: boolean;
  unmountOnExit?: boolean;
  appear?: boolean;
  timeout?: number;
  dimension?: Dimension | (() => Dimension);
  getDimensionValue?: (dimension: Dimension, element: HTMLElement) => number;
  children: ReactElement;
}

const MARGINS: { [d in Dimension]: string[] } = {
  height: ["marginTop", "marginBottom"],
  width: ["marginLeft", "marginRight"],
};

function getDefaultDimensionValue(
  dimension: Dimension,
  elem: HTMLElement
): number {
  const offset = `offset${dimension[0].toUpperCase()}${dimension.slice(1)}`;
  const value = elem[offset];
  const margins = MARGINS[dimension];

  return (
    value +
    // @ts-ignore
    parseInt(css(elem, margins[0]), 10) +
    // @ts-ignore
    parseInt(css(elem, margins[1]), 10)
  );
}

const collapseStyles = {
  [EXITED]: "collapse",
  [EXITING]: "collapsing",
  [ENTERING]: "collapsing",
  [ENTERED]: "collapse show",
};

const Collapse = forwardRef<Transition<any>, CollapseProps>(
  (
    {
      onEnter,
      onEntering,
      onEntered,
      onExit,
      onExiting,
      className,
      children,
      dimension = "height",
      getDimensionValue = getDefaultDimensionValue,
      ...props
    },
    ref
  ) => {
    const isMounted = useMountedState();
    /* Compute dimension */
    const computedDimension =
      typeof dimension === "function" ? dimension() : dimension;

    /* -- Expanding -- */
    const handleEnter = useMemo(
      () =>
        isMounted()
          ? createChainedFunction((elem) => {
              elem.style[computedDimension] = "0";
            }, onEnter)
          : () => null,
      [computedDimension, onEnter]
    );

    const handleEntering = useMemo(
      () =>
        isMounted()
          ? createChainedFunction((elem) => {
              const scroll = `scroll${computedDimension[0].toUpperCase()}${computedDimension.slice(
                1
              )}`;
              elem.style[computedDimension] = `${elem[scroll]}px`;
            }, onEntering)
          : () => null,
      [computedDimension, onEntering]
    );

    const handleEntered = useMemo(
      () =>
        isMounted()
          ? createChainedFunction((elem) => {
              elem.style[computedDimension] = null;
            }, onEntered)
          : () => null,
      [computedDimension, onEntered]
    );

    /* -- Collapsing -- */
    const handleExit = useMemo(
      () =>
        isMounted()
          ? createChainedFunction((elem) => {
              elem.style[computedDimension] = `${getDimensionValue(
                computedDimension,
                elem
              )}px`;
              triggerBrowserReflow(elem);
            }, onExit)
          : () => null,
      [onExit, getDimensionValue, computedDimension]
    );

    const handleExiting = useMemo(
      () =>
        isMounted()
          ? createChainedFunction((elem) => {
              elem.style[computedDimension] = null;
            }, onExiting)
          : () => null,
      [computedDimension, onExiting]
    );

    return (
      <TransitionWrapper
        ref={ref}
        addEndListener={transitionEndListener}
        {...props}
        aria-expanded={props.role ? props.in : null}
        onEnter={handleEnter}
        onEntering={handleEntering}
        onEntered={handleEntered}
        onExit={handleExit}
        onExiting={handleExiting}
        childRef={(children as any).ref}
      >
        {(state: TransitionStatus, innerProps: Record<string, unknown>) =>
          cloneElement(children, {
            ...innerProps,
            className: classNames(
              className,
              children.props.className,
              collapseStyles[state],
              computedDimension === "width" && "collapse-horizontal"
            ),
          })
        }
      </TransitionWrapper>
    );
  }
);

export default Collapse;
