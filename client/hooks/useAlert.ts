import { useContext } from "react";
import { AlertContext, ContextProps } from '../lib/alert';

export const useAlert = (): ContextProps => {
  const context = useContext(AlertContext);
  if (!context) throw new Error("useAlert should only be used inside <Alert />");
  return context;
};
