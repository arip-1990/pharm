import { useContext } from "react";
import { AuthContext, ContextProps } from '../services/auth';

export const useAuth = (): ContextProps => {
  const context = useContext(AuthContext);
  if (!context) throw new Error("useAuth should only be used inside <Auth />");
  return context;
};
