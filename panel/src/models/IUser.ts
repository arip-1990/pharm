export interface IUser {
    id: string;
    name: string;
    phone: string | null;
    email: string | null;
    role: string;
    isAuth: boolean;
}
