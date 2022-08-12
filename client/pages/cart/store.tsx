import { useRouter } from "next/router";
import { FC, useEffect, useState } from "react";
import { useLocalStorage } from "react-use-storage";
import api from "../../lib/api";
import { ICart } from "../../models/ICart";
import defaultImage from "../../assets/images/default.png";
import Layout from "../../components/layout";

const Store: FC = () => {
  const router = useRouter();
  const [carts] = useLocalStorage<ICart[]>("cart", []);
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
        const { data } = await api.get("checkout/store", { params });
        setStores(data);
      } catch (error) {}
      setLoading(false);
    };

    fetchStores();
  }, []);

  return (
    <Layout>
      <div className="row">
        <div className="col-7">
          <span className="border-bottom border-primary">Выбор аптеки</span>
        </div>
        <div className="col-2 text-center">Наличие</div>

        <div className="accordion">
          {stores.map((store) => {
            let price = 0;
            store.products.forEach((product) => {
              price += product.quantity * product.price;
            });

            return (
              <div key={store.store.id} className="store-item">
                <div className="store-item_title">
                  <h6 className="col-7">{store.store.name}</h6>
                  <p className="col-2 text-center text-primary">
                    {store.products.lenght} из $total товаров
                  </p>
                  <p className="col-1 text-end">{price} &#8381;</p>
                  <p className="col-2 text-end">
                    <a
                      href="{{ route('checkout', ['store'=> $store['store']->id]) }}"
                      className="btn btn-primary"
                    >
                      Выбрать аптеку
                    </a>
                  </p>
                </div>
                <div
                  id="collapse-{{ $store['store']->id }}"
                  className="collapse"
                  data-bs-parent=".accordion"
                >
                  <div className="description-item_body row align-items-center">
                    {store.products.map((product) => (
                      <div key={product.product.id}>
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
                </div>
              </div>
            );
          })}
        </div>
      </div>
    </Layout>
  );
};

export default Store;
