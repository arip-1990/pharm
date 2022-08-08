import { ICategory } from "./ICategory";

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
  category: ICategory | null;
  name: string;
  code: number;
  barcode: string | null;
  photos: {
    id: number;
    url: string;
  }[];
  description: string | null;
  status: boolean;
  marked: boolean;
  recipe: boolean;
  attributes: IAttribute[];
}
