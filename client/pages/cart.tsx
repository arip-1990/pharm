import { FC } from "react";
import Layout from "../components/layout";
import defaultImage from "../assets/images/default.png";
import Link from "next/link";
import Head from "next/head";
import { useFetchCartQuery } from "../lib/cartService";
import BaseCart from "../components/cart";

const Cart: FC = () => {
  const { data } = useFetchCartQuery();
  let totalAmount = 0;

  return (
    <Layout>
      <Head>
        <title>Сеть аптек 120/80 | Корзина</title>
        <meta key="description" name="description" content="Корзина." />
      </Head>

      <div className="row">
        <h3>Состав заказа</h3>

        <div className="cart">
          <div className="row cart_header d-md-flex">
            <div className="col-2 text-center" />
            <div className="col-5 text-center">Название</div>
            <div className="col-2 text-center">Цена</div>
            <div className="col-2 text-center">Количество</div>
            <div className="col-1 text-center" />
          </div>
          {data?.map((item) => {
            totalAmount += item.product.minPrice * item.quantity;

            return (
              <div
                key={item.product.id}
                className="row align-items-center product"
              >
                <div className="col-10 col-sm-3 offset-1 offset-sm-0 col-md-2 text-center">
                  {item.product.photos.length ? (
                    <img
                      alt={item.product.name}
                      style={{ width: "100%" }}
                      src={item.product.photos[0].url}
                    />
                  ) : (
                    <img
                      alt={item.product.name}
                      style={{ width: "100%" }}
                      src={defaultImage.src}
                    />
                  )}
                </div>
                <div className="col-10 col-sm-7 col-md-5 product_title">
                  <p>
                    <Link href={`/product/${item.product.slug}`}>
                      <a>{item.product.name}</a>
                    </Link>
                  </p>
                  {/* <p>{{ $item->product->getValue(5) /*?? $product->getValue(38) }}</p> */}
                </div>
                <div className="col-5 col-sm-6 col-md-2 offset-1 offset-sm-0 order-4 order-md-0 text-md-center product_price">
                  <span>от {item.product.minPrice} &#8381;</span>
                </div>
                <div className="col-5 col-sm-6 col-md-2 order-5 order-md-0">
                  <BaseCart
                    productId={item.product.id}
                    style={{ marginLeft: "auto" }}
                  />
                </div>
                <span className="col-2 col-md-1 cart-remove" />
              </div>
            );
          })}

          <div className="row align-items-center mt-3">
            <p className="col-12 col-md-8">
              В процессе оформления заказа цена может незначительно измениться в
              зависимости от выбранной аптеки.
              <br />
              Цены на сайте отличаются от цен в аптеках и действуют только при
              оформлении заказа с помощью сайта.
            </p>
            <p className="col-12 col-md-4 text-center text-md-end fs-4 fw-bold">
              Итого: от <span id="total-price">{totalAmount}</span> &#8381;
            </p>
          </div>

          <div className="row align-items-center mt-3">
            <div className="col-12 col-sm-6 order-sm-1 text-center text-md-end">
              <Link href="/cart/store">
                <a className="btn btn-primary">Оформить заказ</a>
              </Link>
            </div>

            <div className="col-12 col-sm-6 text-center text-md-start mt-3 mt-md-0">
              <a
                href="{{ url('/catalog') }}"
                className="btn btn-outline-primary"
              >
                Вернуться к покупкам
              </a>
            </div>
          </div>
        </div>
      </div>
    </Layout>
  );
};

export default Cart;
