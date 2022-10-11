import { FC, useCallback, useEffect, useState, MouseEvent } from "react";
import Layout from "../components/layout";
import defaultImage from "../assets/images/default.png";
import Link from "next/link";
import Head from "next/head";
import BaseCart from "../components/cart";
import { useLocalStorage } from "react-use-storage";
import { ICart } from "../models/ICart";
import Auth from "../components/auth";
import { useRouter } from "next/router";
import { useAuth } from "../hooks/useAuth";
import { useNotification } from "../hooks/useNotification";
import { useMounted } from "../hooks/useMounted";
import Breadcrumbs from "../components/breadcrumbs";

const Cart: FC = () => {
  const { isAuth } = useAuth();
  const [openModal, setOpenModal] = useState<boolean>(false);
  const [carts] = useLocalStorage<ICart[]>("cart", []);
  const [totalAmount, setTotalAmount] = useState<number>(0);
  const notification = useNotification();
  const isMounted = useMounted();
  const router = useRouter();

  useEffect(() => {
    let tmp = 0;
    carts.forEach((cart) => (tmp += cart.product.minPrice * cart.quantity));
    setTotalAmount(tmp);
  }, [carts]);

  const handleCheckout = useCallback((e: MouseEvent) => {
    e.preventDefault();
    if (isAuth) router.push("/cart/store");
    else {
      setOpenModal(true);
      notification("error", "Авторизуйтесь для оформления заказа");
    }
  }, [isAuth]);

  const getDefaultGenerator = useCallback(() => [
    { href: '/cart', text: "Корзина" }
  ], []);

  return (
    <Layout>
      <Head>
        <title>Сеть аптек 120/80 | Корзина</title>
        <meta key="description" name="description" content="Корзина" />
      </Head>

      <Breadcrumbs getDefaultGenerator={getDefaultGenerator} />

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
          {isMounted() &&
            carts.map((cart) => (
              <div
                key={cart.product.id}
                className="row align-items-center product"
              >
                <div className="col-10 col-sm-3 col-md-2 offset-1 offset-md-0 text-center">
                  {cart.product.photos.length ? (
                    <img
                      alt={cart.product.name}
                      style={{ width: "100%" }}
                      src={cart.product.photos[0].url}
                    />
                  ) : (
                    <img
                      alt={cart.product.name}
                      style={{ width: "100%" }}
                      src={defaultImage.src}
                    />
                  )}
                </div>
                <div className="col-10 col-sm-7 col-md-5 product_title">
                  <p>
                    <Link href={`/catalog/product/${cart.product.slug}`}>
                      <a>{cart.product.name}</a>
                    </Link>
                  </p>
                  {/* <p>{{ $item->product->getValue(5) /*?? $product->getValue(38) }}</p> */}
                </div>
                <div className="col-5 col-sm-6 col-md-2 offset-1 offset-sm-0 order-4 order-md-0 text-md-center product_price">
                  <span>от {cart.product.minPrice} &#8381;</span>
                </div>
                <div className="col-5 col-sm-6 col-md-2 order-5 order-md-0">
                  <BaseCart
                    product={cart.product}
                    style={{ marginLeft: "auto" }}
                  />
                </div>
                <span className="col-2 col-md-1 cart-remove" />
              </div>
            ))}

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
              <a href="#" className="btn btn-primary" onClick={handleCheckout}>
                Оформить заказ
              </a>
            </div>

            <div className="col-12 col-sm-6 text-center text-md-start mt-3 mt-md-0">
              <Link href="/catalog">
                <a className="btn btn-outline-primary">Вернуться к покупкам</a>
              </Link>
            </div>
          </div>
        </div>
      </div>

      <Auth show={openModal} onHide={() => setOpenModal(false)} />
    </Layout>
  );
};

export default Cart;
