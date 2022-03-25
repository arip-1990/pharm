export interface IItem {
    store: {id: string, name: string, slug: string};
    price: number;
    quantity: number;
}

export interface IOffer {
    id: string,
    name: string,
    slug: string,
    items: IItem[];
}
