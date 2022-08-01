export interface IStore {
  id: string;
  slug: string;
  name: string;
  phone?: string;
  schedule: string;
  route?: string;
  delivery: boolean;
  status: boolean;
}
