export interface ICategory {
    id: number;
    parent: number | null;
    name: string;
    slug: string;
    children: ICategory[];
}
