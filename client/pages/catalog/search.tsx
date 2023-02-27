import Layout from "../../templates";
import Card from "../../components/card";
import { FC, useCallback, useEffect } from "react";
import { GetServerSideProps } from "next";
import { ICategory } from "../../models/ICategory";
import Link from "next/link";
import Pagination from "../../components/pagination";
import { wrapper } from "../../store";
import {
  getRunningOperationPromises,
  searchProducts,
  useSearchProductsQuery,
} from "../../lib/catalogService";
import { useRouter } from "next/router";
import {
  fetchCategories,
  useFetchCategoriesQuery,
} from "../../lib/categoryService";
import { useCookie } from "../../hooks/useCookie";
import Breadcrumbs from "../../components/breadcrumbs";
import type { Error } from "../../lib/api";

const generateCategory = (category: ICategory) => {
  return (
    <li key={category.id}>
      <Link href={`/catalog/${category.slug}`}>
        <a>
          {category.picture && <img src={category.picture} alt="" />}
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

const Search: FC = () => {
  const [city] = useCookie("city");
  const router = useRouter();
  const { page, q } = router.query;
  const {
    data: products,
    isFetching,
    error,
    refetch,
  } = useSearchProductsQuery({
    q: q ? String(q) : "",
    page: Number(page) || 1,
  });
  const { data: categories } = useFetchCategoriesQuery();

  const errorData = error as Error;

  const getDefaultGenerator = useCallback(
    () => [
      { href: "/catalog", text: "Наш ассортимент" },
      { href: "/", text: `Поиск по запросу "${q ?? ""}"` },
    ],
    [q]
  );

  useEffect(() => {
    if (products) {
      if (page) router.replace(router.asPath.replace(/[?&]page=\d+/i, ""));
      else refetch();
    }
  }, [city]);

  const generateData = () => {
    if (isFetching)
      return (
        <h4 style={{ textAlign: "center" }}>Идет поиск товара "{q ?? ""}"</h4>
      );
    if (errorData)
      return <h4 style={{ textAlign: "center" }}>{errorData.data.message}</h4>;

    return products?.data.length ? (
      <>
        <div
          className="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-3 g-lg-4"
          itemScope
          itemType="https://schema.org/ItemList"
        >
          <link itemProp="url" href={`/catalog/search?q=${q ?? ""}`} />
          {products?.data.map((product) => (
            <div key={product.id} className="col-10 offset-1 offset-sm-0">
              <Card product={product} />
            </div>
          ))}
        </div>
        <div className="row mt-3">
          <div className="col">
            <Pagination
              currentPage={products?.meta.current_page}
              totalCount={products?.meta.total}
              pageSize={products?.meta.per_page}
            />
          </div>
        </div>
      </>
    ) : (
      <h4 style={{ textAlign: "center" }}>
        По запросу "{q ?? ""}" ничего не найдено!
      </h4>
    );
  };

  return (
    <Layout title="Поиск товара - Сеть аптек 120/80">
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
            {categories?.map((item) => generateCategory(item))}
          </ul>
        </nav>

        <div className="col-md-9 mt-3 mt-md-0">{generateData()}</div>
      </div>
    </Layout>
  );
};

export const getServerSideProps: GetServerSideProps =
  wrapper.getServerSideProps((store) => async ({ params }) => {
    const page = Number(params?.page) || 1;
    const q = params?.q ? String(params?.q) : "";

    store.dispatch(searchProducts.initiate({ q, page }));
    store.dispatch(fetchCategories.initiate());

    await Promise.all(getRunningOperationPromises());

    return { props: {} };
  });

export default Search;
