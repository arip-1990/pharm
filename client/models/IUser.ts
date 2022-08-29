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
    allowEmail: boolean;
    allowSms: boolean;
    balance: number;
    activeBalance: number;
    debet: number;
    credit: number;
    summ: number;
    summDiscounted: number;
    discountSumm: number;
    quantity: number;
    orgUnitName: string;
    preferredOrgUnitName: string;
    registrationDate: moment.Moment | null;
    isAuth: boolean;
}
