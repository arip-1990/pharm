import { createContext } from "react";

export type AccordionEventKey = string | string[] | null | undefined;

export declare type AccordionSelectCallback = (
  eventKey: AccordionEventKey,
  e: React.SyntheticEvent<unknown>
) => void;

export interface AccordionContextValue {
  activeEventKey?: AccordionEventKey;
  onSelect?: AccordionSelectCallback;
}

export const isAccordionItemSelected = (
  activeEventKey: AccordionEventKey,
  eventKey: string
): boolean =>
  Array.isArray(activeEventKey)
    ? activeEventKey.includes(eventKey)
    : activeEventKey === eventKey;

const AccordionContext = createContext<AccordionContextValue>({});

export interface AccordionItemContextValue {
  eventKey: string;
}

const AccordionItemContext = createContext<AccordionItemContextValue>({eventKey: ''});

export {AccordionContext, AccordionItemContext}
