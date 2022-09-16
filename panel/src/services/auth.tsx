import { useState, createContext, ReactNode, FC, useEffect } from "react";
import { IUser } from "../models/IUser";
import axios from "axios";
import api, { API_URL } from "./api";
import moment from "moment";

export interface ContextProps {
  user: null | IUser;
  isAuth: null | boolean;
  login: (login: string, password: string, remember?: boolean) => Promise<void>;
  logout: () => Promise<void>;
  setUser: (user: IUser, isAuth?: boolean) => void;
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

  useEffect(() => {
    const checkAuth = async () => {
      if (isAuth === null) {
        try {
          await revalidate();
        } catch (error) {
          if (axios.isAxiosError(error)) {
            if (error.response && error.response.status === 401) {
              setAuthState({ user: null, isAuth: false });
            }
          }
        }
      }
    };

    checkAuth();
  }, [authState]);

  const csrf = () => axios.get(`${API_URL}/sanctum/csrf-cookie`);

  const login = (login: string, password: string, remember: boolean = false) =>
    new Promise<void>(async (resolve, reject) => {
      try {
        // Get CSRF cookie.
        await csrf();

        // Sign in.
        await api.post(
          "/v1/auth/login",
          { email: login, password, remember },
          { maxRedirects: 0 }
        );

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
        await api.post("/v1/auth/logout");
        // Only sign out after the server has successfully responded.
        setAuthState({ user: null, isAuth: false });
        resolve();
      } catch (error) {
        return reject(error);
      }
    });

  const setUser = (user: IUser, isAuth: boolean = true) => {
    if (user.emailVerified) user.emailVerified = moment(user.emailVerified);
    if (user.phoneVerified) user.phoneVerified = moment(user.phoneVerified);

    setAuthState({ user, isAuth });
  };

  const revalidate = () =>
    new Promise<void>(async (resolve, reject) => {
      try {
        const { data } = await api.get<IUser>("/v1/auth/user", {
          maxRedirects: 0,
        });

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

  return (
    <AuthContext.Provider
      value={{ user, isAuth, login, logout, setUser }}
      children={children}
    />
  );
};

export { Auth, AuthContext };
