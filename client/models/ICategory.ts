export interface ICategory {
    id: number;
    parent: number | null;
    name: string;
    slug: string;
    picture: string | null;
    children: ICategory[];
}
