import { IProduct } from "./IProduct";

export interface ICart {
  product: IProduct;
  quantity: number;
  price?: number;
  discountStorePrice?: number|null;
}
