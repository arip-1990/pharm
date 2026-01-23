import { IProduct } from "./IProduct";

export interface ICart {
  product: IProduct;
  quantity: number;
  price?: number;
  discountStorePrice?: number|null;
  isComboValid?: null|IsComboValid
}

export interface IsComboValid {
  key:string;
  value:boolean;
}


export interface ICartNew {

  data : ICart;
  totalPrice: number|null;
  totalDiscountPrice: number|null;

}
