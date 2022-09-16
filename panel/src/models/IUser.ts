import moment from "moment";

export interface IUser {
    id: string;
    first_name: string;
    last_name: string | null;
    middle_name: string | null;
    email: string;
    phone: string;
    gender: number;
    birth_date: moment.Moment | null;
    phone_verified_at: moment.Moment | null;
    email_verified_at: moment.Moment | null;
    isAuth: boolean;
}
