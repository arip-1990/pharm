import moment from "moment";

interface IAttribute {
    attrubuteName: string;
    attrubuteType: string;
    value: string;
}

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
    photos: {
        id: number;
        url: string;
    }[];
    description: string | null;
    status: string;
    marked: boolean;
    attributes: IAttribute[];
    createdAt: moment.Moment;
    updatedAt: moment.Moment;
}
