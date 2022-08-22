import { useState, createContext, ReactNode, FC, useCallback } from "react";

type alertType = "success" | "warning" | "danger";

export interface ContextProps {
  alerts: { type: alertType; message: string }[];
  addAlert: (type: alertType, message: string) => void;
  clearAlerts: () => void;
}

const AlertContext = createContext<ContextProps | undefined>(undefined);

interface Props {
  children?: ReactNode;
}

const Alert: FC<Props> = ({ children }) => {
  const [alerts, setAlerts] = useState<{ type: alertType; message: string }[]>(
    []
  );

  const addAlert = useCallback((type: alertType, message: string) => {
    setAlerts((item) => [...item, { type, message }]);
  }, []);

  const clearAlerts = useCallback(() => {
    setAlerts([]);
  }, []);

  return (
    <AlertContext.Provider
      value={{ alerts, addAlert, clearAlerts }}
      children={children}
    />
  );
};

export { Alert, AlertContext };
