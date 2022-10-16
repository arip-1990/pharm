import { useState, createContext, ReactNode, FC, useEffect } from "react";
import { IUser } from "../models/IUser";
import axios from "axios";
import api from "./api";
import moment from "moment";

export interface ContextProps {
  user: null | IUser;
  isAuth: null | boolean;
  login: (login: string, password: string) => Promise<void>;
  logout: () => Promise<void>;
  setUser: (user: IUser, isAuth?: boolean) => void;
}

const AuthContext = createContext<ContextProps | undefined>(undefined);

interface Props {
  children?: ReactNode;
}

interface AuthState {
  user: null | IUser;
  isAuth: null | boolean;
}

const Auth: FC<Props> = ({ children }) => {
  const [authState, setAuthState] = useState<AuthState>({
    user: null,
    isAuth: null,
  });

  useEffect(() => {
    const checkAuth = async () => {
      if (authState.isAuth === null) {
        try {
          await revalidate();
        } catch (error) {
          if (axios.isAxiosError(error)) {
            if (error.response && error.response.status === 401) {
              setAuthState({ user: null, isAuth: false });
              localStorage.removeItem("token");
            }
          }
        }
      }
    };

    checkAuth();
  }, [authState]);

  const login = (email: string, password: string) =>
    new Promise<void>(async (resolve, reject) => {
      try {
        // Sign in.
        const { data } = await api.post<{
          accessToken: string;
          expiresIn: number;
        }>("/auth/login", { email, password }, { maxRedirects: 0 });

        localStorage.setItem("token", JSON.stringify(data));

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
        await api.post("/auth/logout");
        // Only sign out after the server has successfully responded.
        setAuthState({ user: null, isAuth: false });
        localStorage.removeItem("token");
        resolve();
      } catch (error) {
        return reject(error);
      }
    });

  const setUser = (user: IUser, isAuth: boolean = true) => {
    if (user.birth_date) user.birth_date = moment(user.birth_date);
    if (user.phone_verified_at)
      user.phone_verified_at = moment(user.phone_verified_at);
    if (user.email_verified_at)
      user.email_verified_at = moment(user.email_verified_at);

    setAuthState({ user, isAuth });
  };

  const revalidate = () =>
    new Promise<void>(async (resolve, reject) => {
      try {
        const { data } = await api.get<IUser>("/auth/user", {
          maxRedirects: 0,
        });

        setUser(data);
        resolve();
      } catch (error) {
        if (axios.isAxiosError(error)) {
          if (error.response && error.response.status === 401) {
            // If there's a 401 error the user is not signed in.
            setAuthState({ user: null, isAuth: false });
            localStorage.removeItem("token");
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
      value={{
        user: authState.user,
        isAuth: authState.isAuth,
        login,
        logout,
        setUser,
      }}
      children={children}
    />
  );
};

export { Auth, AuthContext };
