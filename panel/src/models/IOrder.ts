import moment from "moment";

export interface IStatus {
    value: string;
    state: number;
    createdAt: moment.Moment;
}

export interface IItem {
    product: {id: string, name: string, slug: string};
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
    user: {
        id: string;
        name: string;
    };
    store: {
        id: string;
        name: string;
    };
    statuses: IStatus[];
    items: IItem[];
}
