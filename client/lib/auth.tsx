import { useState, createContext, ReactNode, FC } from "react";
import { IUser } from "../models/IUser";
import axios from "axios";
import api, { API_URL } from "./api";

export interface ContextProps {
  user: null | IUser;
  isAuth: null | boolean;
  login: (login: string, password: string) => Promise<void>;
  logout: () => Promise<void>;
  setUser: (user: IUser, isAuth?: boolean) => void;
  checkAuth: () => Promise<boolean>;
}

const AuthContext = createContext<ContextProps | undefined>(undefined);

interface Props {
  children?: ReactNode;
}

const Auth: FC<Props> = ({ children }) => {
  const [authState, setAuthState] = useState<{
    user: IUser | null;
    isAuth: boolean | null;
  }>({ user: null, isAuth: null });
  const user = authState.user;
  const isAuth = authState.isAuth;

  const csrf = () => axios.get(`${API_URL}/sanctum/csrf-cookie`);

  const login = (login: string, password: string) =>
    new Promise<void>(async (resolve, reject) => {
      try {
        // Get CSRF cookie.
        await csrf();

        // Sign in.
        await api.post("login", { login, password }, { maxRedirects: 0 });

        // Fetch user.
        await revalidate();

        return resolve();
      } catch (error) {
        return reject(error);
      }
    });

  const logout = () =>
    new Promise<void>(async (resolve, reject) => {
      try {
        await api.post("logout");
        // Only sign out after the server has successfully responded.
        setAuthState({ user: null, isAuth: false });
        resolve();
      } catch (error) {
        return reject(error);
      }
    });

  const setUser = (user: IUser, isAuth: boolean = true) =>
    setAuthState({ user, isAuth });

  const revalidate = () =>
    new Promise<void>(async (resolve, reject) => {
      try {
        const { data } = await api.get<IUser>("", { maxRedirects: 0 });

        setUser(data);
        resolve();
      } catch (error) {
        if (axios.isAxiosError(error)) {
          if (error.response && error.response.status === 401) {
            // If there's a 401 error the user is not signed in.
            setAuthState({ user: null, isAuth: false });
            return resolve();
          } else {
            // If there's any other error, something has gone wrong.
            return reject(error);
          }
        } else {
          return reject(error);
        }
      }
    });

  const checkAuth = () =>
    new Promise<boolean>(async (resolve, reject) => {
      if (isAuth === null) {
        // The status is null if we haven't checked it, so we have to make a request.
        try {
          await revalidate();
          return resolve(true);
        } catch (error) {
          if (axios.isAxiosError(error)) {
            if (error.response && error.response.status === 401) {
              // If there's a 401 error the user is not signed in.
              setAuthState({ user: null, isAuth: false });
              return resolve(false);
            } else {
              // If there's any other error, something has gone wrong.
              return reject(error);
            }
          } else {
            return reject(error);
          }
        }
      } else {
        // If it has been checked with the server before, we can just return the state.
        return resolve(isAuth);
      }
    });

  return (
    <AuthContext.Provider
      value={{ user, isAuth, login, logout, setUser, checkAuth }}
      children={children}
    />
  );
};

export { Auth, AuthContext };
