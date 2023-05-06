export interface IBanner {
  id: number;
  title: string;
  description: string;
  picture: { main: string, mobile: string };
  type: 'main' | 'all';
  sort: number;
}
