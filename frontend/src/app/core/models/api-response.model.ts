export interface ApiResponse<T> {
  success: boolean;
  message: string;
  data: T;
  errors: any;
}

export interface PaginatedResponse<T> {
  success: boolean;
  message: string;
  data: {
    data: T[];
    meta: PaginationMeta;
    links: PaginationLinks;
  };
  errors: any;
}

export interface PaginationMeta {
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
  from: number;
  to: number;
}

export interface PaginationLinks {
  first: string;
  last: string;
  prev: string | null;
  next: string | null;
}
