type Key = {
    [key: string]: any
}

type Pagination<T> = {
    title?: string;
    data: T[];
    pagination?: {
        current: number;
        pageSize: number;
        total: number;
    };
    meta?: {
        current_page: number;
        per_page: number;
        total: number;
    }
}
