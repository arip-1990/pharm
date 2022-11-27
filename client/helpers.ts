import { ICategory } from "./models/ICategory";

export const getCategoryById = (id: number, categories: ICategory[]): ICategory | undefined => {
	for (const item of categories) {
		if (item.id == id) return item;

		const found = getCategoryById(id, item.children);
		if (found) return found;
	}
};

export const getCategoryBySlug = (slug: string, categories: ICategory[]): ICategory | undefined => {
	for (const item of categories) {
		if (item.slug == slug) return item;

		const found = getCategoryBySlug(slug, item.children);
		if (found) return found;
	}
};

export const getTreeCategories = (category: ICategory, categories: ICategory[]) => {
	const parent = getCategoryById(category.parent, categories);
	if (parent) return [...getTreeCategories(parent, categories), category];

	return [category];
}
