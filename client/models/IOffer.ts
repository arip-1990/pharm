import { IStore } from "./IStore";

export interface IOffer {
  id: number,
  price: number,
  quantity: number,
  store: IStore
}
