import { useRouter } from "next/router";
import { FC, MouseEvent, useEffect, useState } from "react";
import { useLocalStorage } from "react-use-storage";
import api from "../../lib/api";
import { ICart } from "../../models/ICart";
import defaultImage from "../../assets/images/default.png";
import Layout from "../../components/layout";
import Accordion from "../../components/accordion";
import { useCookie } from "../../hooks/useCookie";

const Store: FC = () => {
  const [city] = useCookie("city");
  const router = useRouter();
  const [carts, setCarts] = useLocalStorage<ICart[]>("cart", []);
  const [loading, setLoading] = useState<boolean>(false);
  const [stores, setStores] = useState<any[]>([]);

  useEffect(() => {
    if (!carts.length) router.back();

    const fetchStores = async () => {
      setLoading(true);
      let params = {};
      carts.forEach((cart) => {
        params[cart.product.id] = cart.quantity;
      });
      try {
        const { data } = await api.post("/order/checkout/store", { ...params });
        setStores(data);
      } catch (error) {}
      setLoading(false);
    };

    fetchStores();
  }, [city]);

  const handleStore = (e: MouseEvent<HTMLButtonElement>, storeId: string) => {
    e.stopPropagation();

    let newCarts = [];
    stores.forEach((store) => {
      if (store.store.id === storeId) {
        newCarts = store.products.map((product) => ({
          product: product.product,
          quantity: product.quantity,
          price: product.price,
        }));
      }
    });
    setCarts(newCarts);
    router.push(`/cart/checkout/${storeId}`);
  };

  return (
    <Layout>
      <div className="row">
        <div className="col-7">
          <span className="border-bottom border-primary">Выбор аптеки</span>
        </div>
        <div className="col-2 text-center">Наличие</div>

        <Accordion>
          {stores.map((store) => {
            let price = 0;
            store.products.forEach((product) => {
              price += product.quantity * product.price;
            });

            return (
              <Accordion.Item
                key={store.store.id}
                eventKey={store.store.id}
                className="store-item"
              >
                <Accordion.Header className="store-item_title" icon="left">
                  <h6 className="col-6">{store.store.location?.address}</h6>
                  <p className="col-2 text-center text-primary">
                    {store.products.length} из {carts.length} товаров
                  </p>
                  <p className="col-1 text-end">{price} &#8381;</p>
                  <p className="col-2 text-end">
                    <button
                      className="btn btn-primary"
                      onClick={(e) => handleStore(e, store.store.id)}
                    >
                      Выбрать аптеку
                    </button>
                  </p>
                </Accordion.Header>
                <Accordion.Body>
                  <div className="description-item_body">
                    {store.products.map((product) => (
                      <div
                        key={product.product.id}
                        className="row align-items-center"
                      >
                        <img
                          className="col-1"
                          src={product.product.photos[0]?.url || defaultImage}
                          alt={product.product.name}
                        />
                        <div className="col-6">{product.product.name}</div>
                        <div className="col-2 text-center">
                          {product.price} &#8381; x {product.quantity}
                        </div>
                        <div className="col-3"></div>
                      </div>
                    ))}
                  </div>
                </Accordion.Body>
              </Accordion.Item>
            );
          })}
        </Accordion>
      </div>
    </Layout>
  );
};

export default Store;
