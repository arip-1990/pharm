import moment from "moment";

export interface ICard {
  id: string;
  number: string;
  bonusType: number;
  cardType: string;
  statusDate: moment.Moment;
  expiryDate: moment.Moment;
  statusCode: number;
  techType: number;
  collaborationType: number;
  balance: number;
  activeBalance: number;
  debet: number;
  credit: number;
  summ: number;
  summDiscounted: number;
  discount: number;
  discountSumm: number;
  quantity: number;
  partnerId: string;
  partnerName: string;
  orgUnitId: string;
  orgUnitName: string;
}
