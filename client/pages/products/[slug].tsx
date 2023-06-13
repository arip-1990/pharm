import { FC, useEffect, useState } from "react";
import { GetServerSideProps } from "next";
import { useRouter } from "next/router";

import Layout from "../../templates";
import Card from "../../components/card";
import { ICategory } from "../../models/ICategory";
import Pagination from "../../components/pagination";
import { wrapper } from "../../store";
import {
  fetchProducts,
  getRunningQueriesThunk,
  useFetchProductsQuery,
} from "../../lib/productService";
import { useCookie } from "../../hooks/useCookie";
import Breadcrumbs from "../../components/breadcrumbs";
import { getCategoryBySlug, getTreeCategories } from "../../helpers";
import { useFetchCategoriesQuery } from "../../lib/categoryService";
import { Category } from "../../templates/category";

const Products: FC = () => {
  const [city] = useCookie("city");
  const router = useRouter();
  const { slug, page } = router.query;
  const [category, setCategory] = useState<ICategory>();
  const { data: categories } = useFetchCategoriesQuery();
  const { data: products, isFetching, refetch } = useFetchProductsQuery({
    category: String(slug),
    page: Number(page),
  });

  useEffect(() => {
    if (categories) setCategory(getCategoryBySlug(String(slug), categories));
  }, [categories]);

  const getDefaultGenerator = () => {
    if (category) {
      return [
        { href: "/products", text: "Наш ассортимент" },
        ...getTreeCategories(category, categories).map((item) => ({
          href: `/products/${item.slug}`,
          text: item.name,
        })),
      ];
    }
    return [{ href: "/products", text: "Наш ассортимент" }];
  };

  useEffect(() => {
    const path = router.asPath.split("?")[0];
    if (Number(page) > 1) router.push(path);
    else refetch();
  }, [city]);

  return (
    <Layout
      title={
        category ? `${category.name} - Сеть аптек 120/80` : "Сеть аптек 120/80"
      }
      loading={isFetching}
    >
      <Breadcrumbs getDefaultGenerator={getDefaultGenerator} />

      <div className="row">
        <nav className="col-md-3">
          <Category data={category.children} />
        </nav>

        <div className="col-md-9 mt-3 mt-md-0">
          {products?.data.length ? (
            <>
              <div
                className="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-3 g-lg-4"
                itemScope
                itemType="https://schema.org/ItemList"
              >
                <link itemProp="url" href="/products" />
                {products?.data.map((product) => (
                  <div key={product.id} className="col-10 offset-1 offset-sm-0">
                    <Card product={product} />
                  </div>
                ))}
              </div>
              <div className="row mt-3">
                <div className="col">
                  <Pagination
                    currentPage={products?.pagination?.current}
                    totalCount={products?.pagination?.total}
                    pageSize={products?.pagination?.pageSize}
                  />
                </div>
              </div>
            </>
          ) : (
            <h3 className="text-center">Товары отсутствуют</h3>
          )}
        </div>
      </div>
    </Layout>
  );
};

export const getServerSideProps: GetServerSideProps = wrapper.getServerSideProps(
  (store) => async ({ params }) => {
    const { page, slug } = params;

    store.dispatch(
      fetchProducts.initiate({ category: String(slug), page: Number(page) })
    );

    await Promise.all(store.dispatch(getRunningQueriesThunk()));

    return { props: {} };
  }
);

export default Products;
