import moment from "moment";
import { IProduct } from "./IProduct";
import { IStore } from "./IStore";
import { IUser } from "./IUser";

export interface IStatus {
    value: string;
    state: number;
    createdAt: moment.Moment;
}

export interface IItem {
    product: IProduct;
    price: number;
    quantity: number;
}

export interface IOrder {
    id: number;
    otherId: number;
    cost: number;
    paymentType: number;
    deliveryType: number;
    deliveryAddress: string;
    status: string;
    note: string | null;
    cancel_reason: string | null;
    createdAt: moment.Moment;
    updatedAt: moment.Moment;
    user: IUser;
    store: IStore;
    statuses: IStatus[];
    items: IItem[];
}
