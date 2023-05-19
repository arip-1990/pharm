import moment from "moment";
import { IOffer } from "./IOffer";
import { IPhoto } from "./IPhoto";

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
  barcodes: string[];
  photos: IPhoto[];
  description: string | null;
  marked: boolean;
  recipe: boolean;
  sale: boolean;
  status: boolean;
  showMain: boolean;
  offers: IOffer[];
  totalOffers: number;
  attributes: IAttribute[];
  createdAt: moment.Moment;
  updatedAt: moment.Moment;
}
