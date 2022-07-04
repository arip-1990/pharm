import { ICategory } from '../models/ICategory';
import api from '../services/api';

export async function getCategories() {
  const { data } = await api.get<ICategory[]>('category');
  
  return data;
}
