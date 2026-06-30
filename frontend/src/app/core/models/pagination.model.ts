export interface QueryParams {
  page?: number;
  per_page?: number;
  search?: string;
  sort?: string;
  order?: 'asc' | 'desc';
  status?: string;
  [key: string]: any;
}
