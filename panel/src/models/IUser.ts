export interface IUser {
    id: string;
    firstName: string;
    lastName: string;
    middleName: string | null;
    phone: string | null;
    email: string | null;
    post: string;
    role: string;
    stores: { id: string; name: string; }[];
    isAuth: boolean;
}
