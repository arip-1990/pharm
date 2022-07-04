import { FC } from "react";
import { useSanctum } from "react-sanctum";
import Layout from "../../../components/layout";
import Sidebar from "../../../components/sidebar";
import { useGetOrderQuery } from "../../../services/orderService";
import defaultImage from "../../../assets/images/default.png";

const Order: FC<{ id: number }> = ({ id }) => {
  const { user } = useSanctum();
  const { data: order } = useGetOrderQuery(id);

  return (
    <Layout>
      <div className="row">
        <Sidebar className="col-12 col-lg-3" />
        <div className="col-12 col-lg-9">
          <ul className="list-group list-group-flush">
            <li className="list-group-item">
              <h4>Личные данные</h4>
              <p>
                <b className="text-secondary">Имя: </b>
                {user?.name}
              </p>
              <p>
                <b className="text-secondary">Телефон: </b>
                {user?.phone}
              </p>
            </li>
            <li className="list-group-item">
              <h4>Точка самовывоза</h4>
              <p>
                <b className="text-secondary">Адрес самовывоза: </b>
                {order.store.address}
              </p>
              <p>
                <b className="text-secondary">Мобильный телефон: </b>
                {order.store.phone}
              </p>
              <p>
                <b className="text-secondary">Время работы: </b>
                {order.store.schedule}
              </p>
              <p>
                <b className="text-secondary">Способ оплаты: </b>
                {order.paymentType}
              </p>
              <p>
                <b className="text-secondary">Сумма заказа: </b>
                {order.cost}&#8381;
              </p>
            </li>
            <li className="list-group-item">
              <h4>Товары</h4>
              <div className="row row-cols-sm-3">
                {order.items.map((item) => (
                  <div
                    key={item.product.id}
                    className="col"
                    style={{ textAlign: "center" }}
                  >
                    <p>
                      <a href="{{ route('catalog.product', $item->product) }}">
                        {item.product.photos.length ? (
                          <img
                            alt={item.product.name}
                            width={150}
                            src={item.product.photos[0].url}
                          />
                        ) : (
                          <img
                            alt={item.product.name}
                            width={150}
                            src={defaultImage.src}
                          />
                        )}
                        <br />
                        <span>{item.product.name}</span>
                      </a>
                    </p>
                    <span className="text-muted">
                      {item.price}&#8381; x {item.quantity}
                    </span>
                  </div>
                ))}
              </div>
            </li>
          </ul>
        </div>
      </div>
    </Layout>
  );
};

export default Order;
