import { useContext } from "react";
import { AuthContext, ContextProps } from '../lib/auth';

interface useAuthReturn<T> extends ContextProps {
  user: T;
}

export default function useAuth<T = null | any>(): useAuthReturn<T> {
  const context = useContext(AuthContext);
  if (!context) throw new Error("useAuth should only be used inside <Auth />");
  return context;
};
