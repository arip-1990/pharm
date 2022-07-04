import moment from "moment";

export interface IStore {
  id: string;
  slug: string;
  name: string;
  phone?: string;
  address?: string;
  schedule: string;
  coordinate: [number, number];
  route?: string;
  delivery: boolean;
  status: boolean;
}
