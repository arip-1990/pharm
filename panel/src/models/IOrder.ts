import moment from "moment";

export interface IStatus {
    value: string;
    state: number;
    createdAt: moment.Moment;
}

export interface IOrder {
    id: number;
    cost: number;
    paymentType: number;
    deliveryType: number;
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
}
