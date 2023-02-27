import React from "react";
import { Link, useParams } from "react-router-dom";
import { Card, Col, Row, Table } from "antd";
import {
  useFetchOrderItemsQuery,
  useFetchOrderQuery,
} from "../../services/OrderService";
import StatusStep from "./StatusStep";

const baseColumns = [{ dataIndex: "key" }, { dataIndex: "value" }];

const itemColumns = [
  { dataIndex: "name", title: "Наименование товара" },
  { dataIndex: "quantity", title: "Количество" },
  {
    dataIndex: "price",
    title: "Цена",
    render: (price: number) => <span>{price}&#8381;</span>,
  },
  {
    dataIndex: "total",
    title: "Итого",
    render: (total: number) => <span>{total}&#8381;</span>,
  },
];

const View: React.FC = () => {
  const { id } = useParams();
  const { data: order, isFetching: orderLoading } = useFetchOrderQuery(
    Number(id),
    { skip: !id }
  );
  const {
    data: items,
    isFetching: orderItemsLoading,
  } = useFetchOrderItemsQuery(Number(id), { skip: !id });

  return (
    <Row gutter={[32, 32]}>
      <Col span={24}>
        <h2 style={{ margin: 0 }}>Заказ {order?.id}</h2>
      </Col>

      <Col span={24}>
        <Card title="Общий">
          <Table
            size="small"
            loading={orderLoading}
            showHeader={false}
            pagination={false}
            columns={baseColumns}
            dataSource={
              order && [
                { key: "Номер заказа в 1c", value: order.otherId },
                { key: "Время заказа", value: order.createdAt.format("LLL") },
                {
                  key: "Статус",
                  value: (
                    <StatusStep
                      full
                      statuses={order.statuses}
                      paymentType={order.paymentType}
                      deliveryType={order.deliveryType}
                    />
                  ),
                },
                {
                  key: 'Платформа',
                  value: order.platform
                },
                {
                  key: "Тип оплаты / Тип доставки",
                  value:
                    (order.paymentType === "card"
                      ? "Оплата картой"
                      : "Наличными") +
                    " / " +
                    (order.deliveryType === "delivery"
                      ? "Доставка"
                      : "Самовывоз"),
                },
                { key: "Адрес доставки", value: order.deliveryAddress },
                {
                  key: "Сумма заказа",
                  value: (
                    <span>{(order.cost * 1).toLocaleString("ru")}&#8381;</span>
                  ),
                },
                { key: "Аптека", value: order.store.name },
                {
                  key: "Оплачено",
                  value: order.statuses.some(
                    (item) => item.value === "P" && item.state === 2
                  )
                    ? "Да"
                    : "Нет",
                },
                {
                  key: "Заказчик",
                  value: order.customer.name,
                },
                { key: "Заметка", value: order.note },
              ]
            }
          />
        </Card>
      </Col>

      <Col span={24}>
        <Card title="Товары">
          <Table
            size="small"
            loading={orderLoading}
            pagination={false}
            columns={itemColumns}
            dataSource={order?.items.map((item, i) => ({
              key: i + 1,
              name: (
                <Link to={`/product/${item.product.slug}`}>
                  {item.product.name}
                </Link>
              ),
              quantity: item.quantity,
              price: (item.price * 1).toLocaleString("ru"),
              total: (item.quantity * item.price).toLocaleString("ru"),
            }))}
          />
        </Card>
      </Col>

      <Col span={24}>
        <Card title="Всего заказанных товаров">
          <Table
            size="small"
            loading={orderItemsLoading}
            pagination={false}
            columns={itemColumns}
            dataSource={items?.map((item, i) => ({
              key: i + 1,
              name: (
                <Link to={`/product/${item.product.slug}`}>
                  {item.product.name}
                </Link>
              ),
              quantity: item.quantity,
              price: (item.price * 1).toLocaleString("ru"),
              total: (item.quantity * item.price).toLocaleString("ru"),
            }))}
          />
        </Card>
      </Col>
    </Row>
  );
};

export default View;
