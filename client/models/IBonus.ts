import moment from "moment";

export interface IBonus {
  id: string;
  operationType: number;
  debet: number;
  credit: number;
  remainder: number;
  cardId: string;
  cardNumber: string;
  chequeId: string;
  chequeNumber: string;
  partnerId: string;
  partnerName: string;
  campaignId: string;
  campaignName: string;
  ruleId: string;
  ruleName: string;
  chequeItemId: string;
  chequeItemProductName: string;
  chequeItemParentId: string;
  chequeItemParentNumber: string;
  parentType: number;
  parentName: string;
  createdDate: moment.Moment;
  actualStart: moment.Moment;
  actualEnd: moment.Moment;
}
