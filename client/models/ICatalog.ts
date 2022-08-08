import { ICategory } from "./ICategory";
import { IProduct } from "./IProduct";

export interface ICatalog {
    categories: ICategory[],
    products: IProduct[],
    meta: {
        current_page: number;
        per_page: number;
        total: number;
    }
}
