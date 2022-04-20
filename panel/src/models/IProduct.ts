import moment from "moment";

interface IAttribute {
  id: number;
  name: string;
  type: string;
  variants: string[];
  value: string;
}

export interface IProduct {
  id: string;
  slug: string;
  category: {
    id: number;
    name: string;
  } | null;
  name: string;
  code: number;
  barcode: string | null;
  photos: {
    id: number;
    sort: number;
    url: string;
  }[];
  description: string | null;
  marked: boolean;
  recipe: boolean;
  sale: boolean;
  status: boolean;
  attributes: IAttribute[];
  createdAt: moment.Moment;
  updatedAt: moment.Moment;
}
