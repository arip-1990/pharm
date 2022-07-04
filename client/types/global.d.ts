type Key = {
    [key: string]: any
}

type Pagination<T> = {
    data: T[];
    meta: {
        current_page: number;
        per_page: number;
        total: number;
    }
}
