import {ICategory} from "../models/ICategory";

const getCategoryById = (id: number, categories: ICategory[]): ICategory | undefined => {
	for (const item of categories) {
		if (item.id == id) return item;

		const found = getCategoryById(id, item.children);
		if (found) return found;
	}
};
