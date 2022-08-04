import { IStore } from "./IStore";

export interface IUser {
    id: string;
    firstName: string;
    lastName: string | null;
    middleName: string | null;
    email: string;
    phone: string;
    gender: string;
    birthDate: moment.Moment | null;
    emailVerified: boolean;
    phoneVerified: boolean;
    isAuth: boolean;
}
