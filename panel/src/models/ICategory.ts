export interface ICategory {
    id: number;
    parent: number | null;
    name: string;
    description: string | null;
    type: string;
    children: ICategory[];
}
