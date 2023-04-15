import moment from "moment";

export interface IPhoto {
  id: number;
  title: string;
  url: string;
  sort: number;
  type: string;
  createdAt: moment.Moment;
  updatedAt: moment.Moment;
}
