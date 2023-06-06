export interface IBanner {
  id: number;
  title: string;
  description: string | null;
  picture: { main: string; mobile: string | null };
  type: number;
  sort: number;
  path: string | null; // new
}
