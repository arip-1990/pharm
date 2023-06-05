export interface IBanner {
  id: number;
  title: string;
  description: string;
  picture: { main: string, mobile: string };
  type: number;
  sort: number;
  path:string // new
}
