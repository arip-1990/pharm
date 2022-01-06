import moment from "moment";

export interface IProduct {
    id: string;
    slug: string;
    category: {
        id: number;
        name: string;
    } | null;
    name: string;
    code: number;
    barcode: string | null;
    photo: string;
    description: string | null;
    status: string;
    marked: boolean;
    createdAt: moment.Moment;
    updatedAt: moment.Moment;
}
