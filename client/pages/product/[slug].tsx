import Layout from "../../components/layout";
import Head from "next/head";
import { GetServerSideProps } from "next";
import { FC, useCallback, useEffect, useState } from "react";
import defaultImage from "../../assets/images/default.png";
import { useLocalStorage } from "react-use-storage";
import Accordion from "../../components/accordion";
import Zoom from "../../components/zoom";
import Cart from "../../components/cart";
import {
  getProduct,
  getRunningOperationPromises,
  useGetProductQuery,
} from "../../lib/catalogService";
import { wrapper } from "../../lib/store";
import api from "../../lib/api";
import { useRouter } from "next/router";

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
  const router = useRouter();
  const { slug } = router.query;
  const { data } = useGetProductQuery(String(slug));

  return (
    <Layout>
      <Head>
        <title>{data?.product.name}</title>
      </Head>

      <Accordion>
        <Accordion.Item eventKey="attributes">
          <article
            className="row justify-content-center mb-3"
            itemScope
            itemType="https://schema.org/Product"
          >
            <div className="col-8 col-sm-7 col-md-5 col-lg-3 position-relative">
              {data?.product.photos.length ? (
                <Zoom src={data?.product.photos[0].url} alt="product.name" />
              ) : (
                <img
                  className="mw-100 m-auto"
                  itemProp="image"
                  src={defaultImage.src}
                  alt={data?.product.name}
                />
              )}

              {isFavorite(data?.product.id)}
            </div>

            <div className="col-12 col-lg-9 d-flex flex-column">
              <div className="row">
                <div className="col-12 col-lg-8 col-xxl-9">
                  <h4 className="text-center mb-3" itemProp="name">
                    {data?.product.name}
                  </h4>
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
                  {data?.offers ? (
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
                    <Accordion.Header className="description-item_title">
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
                    <Accordion.Header className="description-item_title">
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

      {data?.offers ? (
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

          {data?.offers.map((item) => {
            if (item.store) {
              return (
                <div
                  key={item.id}
                  className="row align-items-center border-top p-2 m-0"
                >
                  <div className="col-12 col-md-5">
                    <span style={{ fontWeight: 600 }}>{item.store.name}</span>
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
              );
            }
            return item.store.id;
          })}
        </>
      ) : null}
    </Layout>
  );
};

export const getServerSideProps: GetServerSideProps = wrapper.getServerSideProps(
  (store) => async ({ req, params }) => {
    if (req) api.defaults.headers.get.Cookie = req.headers.cookie;
    const { slug } = params;

    store.dispatch(getProduct.initiate(String(slug)));

    await Promise.all(getRunningOperationPromises());

    return { props: {} };
  }
);

export default Product;
