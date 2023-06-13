export interface IBanner {
  id: number;
  title: string;
  description?: string;
  picture: { main?: string; mobile?: string };
  path: string;
  link?: string;
  type: 'main' | 'extra' | 'all' | 'mobile';
  sort: number;
}
