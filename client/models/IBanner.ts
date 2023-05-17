export interface IBannerPicture {
  main: string | null;
  mobile: string | null;
}

export interface IBanner {
  id: number;
  title: string;
  description: string | null;
  picture: IBannerPicture;
  type: number;
  sort: number;
}
