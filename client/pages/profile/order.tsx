import { FC, useState } from "react";
import Layout from "../../components/layout";
import Sidebar from "../../components/sidebar";
import BaseOrder from "../../components/profile/Order";
import { useFetchOrdersQuery } from "../../lib/orderService";
import Link from "next/link";

const Order: FC = () => {
  const [page, setPage] = useState<number>(1);
  const { data: orders } = useFetchOrdersQuery({ page });

  return (
    <Layout>
      <div className="row">
        <Sidebar className="col-12 col-lg-3" />
        <div className="col-12 col-lg-9">
          <BaseOrder>
            {orders?.data.length ? (
              <ul className="list-group list-group-flush">
                {orders.data.map((item) => (
                  <li className="list-group-item">
                    <h6 style={{ margin: 0 }}>
                      <Link href={`/profile/order/${item.id}`}>
                        <a>Заказ №{item.id}</a>
                      </Link>
                    </h6>
                    <span className="text-secondary">
                      Сумма заказа: {item.cost}&#8381;
                    </span>
                  </li>
                ))}
              </ul>
            ) : (
              <>
                <h4>Здесь будут Ваши заказы</h4>
                <span>Закажите аптечные товары прямо сейчас</span>
              </>
            )}
          </BaseOrder>
        </div>
      </div>
    </Layout>
  );
};

export default Order;
