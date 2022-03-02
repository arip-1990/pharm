import moment from 'moment';

export interface IProduct {
    id: string;
    name: string;
    slug: string;
    description: string|null;
    marked: boolean;
    createdAt: moment.Moment;
    updatedAt: moment.Moment;
    photos: string[];
}