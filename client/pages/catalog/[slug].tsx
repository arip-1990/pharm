import Layout from "../../components/layout";
import Card from "../../components/card";
import {FC, useEffect} from "react";
import { GetServerSideProps } from "next";
import { ICategory } from "../../models/ICategory";
import Image from "next/image";
import Link from "next/link";
import Pagination from "../../components/pagination";
import { useRouter } from "next/router";
import { wrapper } from "../../store";
import {
  fetchProducts,
  getRunningOperationPromises,
  useFetchProductsQuery,
} from "../../lib/catalogService";
import { useCookie } from "../../hooks/useCookie";
import Breadcrumbs from "../../components/breadcrumbs";
import {getCategoryBySlug, getTreeCategories} from "../../helpers";
import {useFetchCategoriesQuery} from "../../lib/categoryService";

const generateCategory = (category: ICategory) => {
  return (
    <li key={category.id}>
      <Link href={`/catalog/${category.slug}`}>
        <a>
          {category.parent ? null : (
            <Image
              width={36}
              height={36}
              src={`/assets/images/category/cat_${category.id}.png`}
              alt=""
            />
          )}
          {category.name}
        </a>
      </Link>
      {category.children.length ? (
        <div className="overlay">
          <ul>
            {category.children
              .filter((_, i) => i < 10)
              .map((item) => generateCategory(item))}
            {category.children.length > 10 ? (
              <li>
                <Link href={`/catalog/${category.slug}`}>
                  <a>{category.name}</a>
                </Link>
              </li>
            ) : null}
          </ul>
        </div>
      ) : null}
    </li>
  );
};

const Catalog: FC = () => {
  const [city] = useCookie("city");
  const router = useRouter();
  const { slug, page } = router.query;
  const {data: categories} = useFetchCategoriesQuery();
  const { data, refetch } = useFetchProductsQuery({
    category: String(slug),
    page: Number(page),
  });

  const getDefaultGenerator = () => {
    if (categories) {
      const category = getCategoryBySlug(String(slug), categories);
      return [{href: '/catalog', text: "Наш ассортимент"}, ...getTreeCategories(category, categories).map(item => ({
        href: `/catalog/${item.slug}`,
        text: item.name
      }))]
    }
    return [{href: '/catalog', text: "Наш ассортимент"}];
  };

  useEffect(() => {
    const path = router.asPath.split("?")[0];
    if (Number(page) > 1) router.push(path);
    else refetch();
  }, [city]);

  return (
    <Layout>
        <Breadcrumbs getDefaultGenerator={getDefaultGenerator} />

      <div className="row">
        <nav className="col-md-3">
          <ul className="category">
            {/* <li className="sale">
              <a href="/catalog/sale">
                <Image width={36} height={36} src={saleImage} alt="" />
                Распродажа
              </a>
            </li> */}
            {data?.categories.map((item) => generateCategory(item))}
          </ul>
        </nav>

        <div className="col-md-9 mt-3 mt-md-0">
          {data?.products.length ? (
            <>
              <div
                className="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-3 g-lg-4"
                itemScope
                itemType="https://schema.org/ItemList"
              >
                <link itemProp="url" href="/catalog" />
                {data?.products.map((product) => (
                  <div key={product.id} className="col-10 offset-1 offset-sm-0">
                    <Card product={product} />
                  </div>
                ))}
              </div>
              <div className="row mt-3">
                <div className="col">
                  <Pagination
                    currentPage={data?.meta.current_page}
                    totalCount={data?.meta.total}
                    pageSize={data?.meta.per_page}
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

    await Promise.all(getRunningOperationPromises());

    return { props: {} };
  }
);

export default Catalog;
