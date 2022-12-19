import { useEffect, useState } from "react";
import { useSanctum } from "react-sanctum";
import moment from 'moment';
import { IUser } from "../models/IUser";

type AuthReturn = {
  user: IUser | null;
  isAuth: boolean | null;
  login: (username: string, password: string, remember?: boolean) => Promise<{ twoFactor: boolean; signedIn: boolean }>;
  logout: () => Promise<void>;
}

const useAuth = (): AuthReturn => {
  const [newUser, setNewUser] = useState<IUser|null>(null);
  const {authenticated, user, signIn, signOut} = useSanctum<IUser>();

  useEffect(() => {
    if (authenticated)
      setNewUser({...user, birth_date: moment(user.birth_date), phone_verified_at: moment(user.phone_verified_at), email_verified_at: moment(user.email_verified_at)});
    else if (authenticated === false)
      setNewUser(null);
  }, [authenticated]);

  return {
    user: newUser,
    isAuth: authenticated,
    login: signIn,
    logout: signOut
  };
};

export { useAuth };
