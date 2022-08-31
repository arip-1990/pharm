import moment from "moment";

export interface ICheque {
  id: string;
  number: string;
  date: moment.Moment;
  partnerId: string;
  partnerName: string;
  orgUnitId: string;
  orgUnitName: string;
  orgUnitFullName: string;
  orgUnitAddress: string;
  operationTypeName: string;
  operationTypeCode: number;
  chequeItemCount: number;
  summ: number;
  bonus: number;
  paidByBonus: number;
  paidByMoney: number;
  cardId: string;
  cardNumber: number;
  discount: number;
  summDiscounted: number;
  score: number;
  lowerBound: number;
  upperBound: number;
}
