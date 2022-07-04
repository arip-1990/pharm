import { IProduct } from '../models/IProduct';
import api from '../services/api';

export async function getPopularProducts() {
  const { data } = await api.get<IProduct[]>('catalog/popular');
  
  return data;
}

type Params = {
  page: string;
  category?: string;
}

export async function getProducts(params: Params) {
  const { data } = await api.get<Pagination<IProduct>>('catalog' + (params.category ? ('/' + params.category) : ''), {
    params: {page: params.page}
  });
  
  return data;
}

export async function getProduct(slug: string) {
  const { data } = await api.get<IProduct>('catalog/product/' + slug);
  
  return data;
}
