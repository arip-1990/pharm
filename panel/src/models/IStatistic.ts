import moment from "moment";

export interface IStatistic {
    id: number;
    ip: string;
    city: string | null;
    os: string;
    browser: string;
    screen: string;
    referrer: string | null;
    createdAt: moment.Moment;
    updatedAt: moment.Moment;
}
