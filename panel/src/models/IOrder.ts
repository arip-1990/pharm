import moment from "moment";

export interface IStatus {
  value: string;
  state: number;
  createdAt: moment.Moment;
}

export interface IItem {
  product: { id: string, name: string, slug: string };
  price: number;
  quantity: number;
}

export interface IOrder {
  id: number;
  otherId: number;
  cost: number;
  paymentType: string;
  deliveryType: string;
  deliveryAddress: string;
  note: string | null;
  cancel_reason: string | null;
  platform: string;
  createdAt: moment.Moment;
  updatedAt: moment.Moment;
  customer: {
    name: string;
    phone: string;
    email: string | null;
  };
  store: {
    id: string;
    name: string;
  };
  statuses: IStatus[];
  items: IItem[];
}
