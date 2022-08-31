import moment from "moment";

export interface ICoupon {
  id: string;
  name: string;
  description: string;
  number: number;
  partnerId: string;
  partnerName: string;
  statusType: number;
  cardId: string;
  isActive: boolean;
  logoUrl: string,
  actualStart: moment.Moment;
  actualEnd: moment.Moment;
}
