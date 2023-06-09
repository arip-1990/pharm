export interface IBannerPicture {
  main: string;
  mobile?: string; // null
}

export interface IBanner {
  id: number;
  title: string;
  description?: string;
  picture: IBannerPicture;
  type: "main" | "extra" | "all" | "mobile";
  path: string;
  link?: string;
  sort: number;
}
