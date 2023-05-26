import { FC, useCallback, useEffect, useState } from "react";
import { GetServerSideProps } from "next";
import { useRouter } from "next/router";
import { useLocalStorage } from "react-use-storage";

import Layout from "../../../templates";
import Accordion from "../../../components/accordion";
import Cart from "../../../components/cart";
import {
  getProduct,
  getRunningQueriesThunk,
  useGetProductQuery,
} from "../../../lib/catalogService";
import { wrapper } from "../../../store";
import { useCookie } from "../../../hooks/useCookie";
import { getTreeCategories } from "../../../helpers";
import { useFetchCategoriesQuery } from "../../../lib/categoryService";
import { Carousel } from "../../../components/carousel";
import Breadcrumbs from "../../../components/breadcrumbs";

import defaultImage from "../../../assets/images/default.png";

const isFavorite = (id: string) => {
  const [isFavorite, setIsFavorite] = useState<boolean>(false);
  const [favorites, setFavorites] = useLocalStorage<string[]>("favorites", []);

  useEffect(() => {
    setIsFavorite(favorites.includes(id));
  }, [favorites]);

  const handleFavorite = useCallback(() => {
    if (isFavorite) setFavorites(favorites.filter((item) => item !== id));
    else setFavorites([...favorites, id]);
  }, [isFavorite]);

  return (
    <i
      className={"icon-heart" + (isFavorite ? "" : "-empty")}
      onClick={handleFavorite}
    />
  );
};

const Product: FC = () => {
  const [showCarousel, setShowCarousel] = useState<boolean>(false);
  const [city] = useCookie("city");
  const router = useRouter();
  const { slug } = router.query;
  const { data, refetch } = useGetProductQuery(String(slug));
  const { data: categories } = useFetchCategoriesQuery();

  const getDefaultGenerator = () => {
    let crumbs = [{ href: "/catalog", text: "Наш ассортимент" }];

    if (categories && data?.product.category) {
      crumbs = [
        ...crumbs,
        ...getTreeCategories(data.product.category, categories).map((item) => ({
          href: `/catalog/${item.slug}`,
          text: item.name,
        })),
      ];
    }

    return [
      ...crumbs,
      { href: `/catalog/product/${slug}`, text: data?.product.name },
    ];
  };

  useEffect(() => {
    refetch();
  }, [city]);

  const handleShowCarousel = () =>
    data.product.photos.length && setShowCarousel(true);

  return (
    <Layout
      title={
        data ? `${data.product.name} - Сеть аптек 120/80` : "Сеть аптек 120/80"
      }
      description={data?.product.description}
    >
      <Breadcrumbs getDefaultGenerator={getDefaultGenerator} />

      <Accordion>
        <Accordion.Item eventKey="attributes">
          <article
            className="row justify-content-center mb-3"
            itemScope
            itemType="https://schema.org/Product"
          >
            <div className="col-8 col-sm-7 col-md-5 col-lg-3 position-relative">
              <img
                style={{ cursor: "zoom-in" }}
                className="mw-100 m-auto"
                itemProp="image"
                src={
                  data?.product.photos.length
                    ? data?.product.photos[0].url
                    : defaultImage.src
                }
                alt={data?.product.name}
                onClick={handleShowCarousel}
              />

              {isFavorite(data?.product.id)}
            </div>

            <div className="col-12 col-lg-9 d-flex flex-column">
              <div className="row">
                <div className="col-12 col-lg-8 col-xxl-9">
                  <h5 className="text-center mb-3" itemProp="name">
                    {data?.product.name}
                  </h5>
                </div>
              </div>

              <div className="row" style={{ minHeight: "50%" }}>
                <div className="col-12 col-lg-8 col-xxl-9 mb-3 mb-lg-0">
                  {data?.product.attributes.length ? (
                    <div style={{ background: "#e6eded", padding: "0.75rem" }}>
                      {data?.product.attributes
                        .filter((item) =>
                          [
                            "Производитель",
                            "Страна",
                            "Действующее вещество",
                            "Условия отпуска из аптек",
                          ].includes(item.name)
                        )
                        .map((item) => (
                          <h6 key={item.id}>
                            <b className="me-2">{item.name}:</b>
                            {item.value}
                          </h6>
                        ))}
                      <h6>
                        <Accordion.Header
                          as="b"
                          className="description-info text-primary"
                        >
                          Информация о товаре
                        </Accordion.Header>
                      </h6>
                    </div>
                  ) : null}
                </div>
                <div className="col-12 col-lg-4 col-xxl-3 d-flex flex-column justify-content-evenly align-items-end">
                  {data?.offers.length ? (
                    <>
                      <h5
                        className="price"
                        itemProp="offers"
                        itemScope
                        itemType="https://schema.org/Offer"
                      >
                        <p itemProp="price">
                          Цена: от{" "}
                          <span
                            style={{ fontSize: "1.75rem", fontWeight: 600 }}
                          >
                            {data?.offers[0].price}
                          </span>{" "}
                          &#8381;
                        </p>
                      </h5>
                      <Cart product={data?.product} />
                    </>
                  ) : (
                    <h4 className="text-center">Нет в наличии</h4>
                  )}
                </div>
              </div>
            </div>
          </article>

          <Accordion.Body>
            <Accordion>
              <div className="description">
                {data?.product.description ? (
                  <Accordion.Item
                    eventKey="description"
                    className="description-item"
                  >
                    <Accordion.Header
                      className="description-item_title"
                      iconType="plus"
                    >
                      Описание
                    </Accordion.Header>
                    <Accordion.Body
                      className="description-item_body"
                      itemProp="description"
                    >
                      <div
                        dangerouslySetInnerHTML={{
                          __html: data?.product.description,
                        }}
                      />
                    </Accordion.Body>
                  </Accordion.Item>
                ) : null}

                {data?.product.attributes.map((item) => (
                  <Accordion.Item
                    key={item.id}
                    eventKey={item.id.toString()}
                    className="description-item"
                  >
                    <Accordion.Header
                      className="description-item_title"
                      iconType="plus"
                    >
                      {item.name}
                    </Accordion.Header>
                    <Accordion.Body className="description-item_body">
                      {item.type === "text" ? (
                        <div dangerouslySetInnerHTML={{ __html: item.value }} />
                      ) : (
                        item.value
                      )}
                    </Accordion.Body>
                  </Accordion.Item>
                ))}
              </div>
            </Accordion>
          </Accordion.Body>
        </Accordion.Item>
      </Accordion>

      {data?.offers.length ? (
        <>
          <div
            className="row p-2 fw-bold d-md-flex m-0"
            style={{
              display: "none",
              background: "#f4f4f4",
              color: "#757a7a",
            }}
          >
            <div className="col-md-5 text-center">Адрес</div>
            <div className="col-md-3 text-center">Время работы</div>
            <div className="col-md-2 text-center">Цена</div>
            <div className="col-md-2 text-center">Количество</div>
          </div>

          {data?.offers.map((item) => (
            <div
              key={item.id}
              className="row align-items-center border-top p-2 m-0"
            >
              <div className="col-12 col-md-5">
                <span style={{ fontWeight: 600 }}>
                  {item.store.location.address}
                </span>
              </div>
              <div className="col-12 col-md-3 text-md-center">
                <b className="d-md-none">Время работы: </b>
                <span
                  dangerouslySetInnerHTML={{ __html: item.store.schedule }}
                />
              </div>
              <div className="col-12 col-md-2 text-md-center">
                <b className="d-md-none">Цена: </b>
                <span style={{ fontSize: "1.25rem", fontWeight: 600 }}>
                  {item.price}
                </span>{" "}
                &#8381;
              </div>
              <div className="col-12 col-md-2 text-md-center">
                <b className="d-md-none">Количество:</b>
                {item.quantity >= 10 ? "много" : item.quantity + " шт."}
              </div>
            </div>
          ))}
        </>
      ) : null}

      <Carousel
        show={showCarousel}
        onHide={() => setShowCarousel(false)}
        data={data?.product.photos.filter((item) => item.url)}
      />
    </Layout>
  );
};

export const getServerSideProps: GetServerSideProps = wrapper.getServerSideProps(
  (store) => async ({ params }) => {
    const { slug } = params;

    store.dispatch(getProduct.initiate(String(slug)));

    await Promise.all(store.dispatch(getRunningQueriesThunk()));

    return { props: {} };
  }
);

export default Product;
