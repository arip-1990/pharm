export interface IPagination<T> {
    current: number;
    pageSize: number;
    total: number;
    data: T[]
}
