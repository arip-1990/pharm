export interface IOffer {
  id: string,
  price: number;
  quantity: number;
  store: { id: string, name: string, slug: string };
}
