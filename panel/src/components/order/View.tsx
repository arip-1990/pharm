import React from "react";
import { useParams } from "react-router-dom";
import { Card, Col, Row, Table } from "antd";
import { useFetchOrderQuery } from "../../services/OrderService";
import StatusStep from './StatusStep';

const baseColumns = [
  { dataIndex: "key" },
  { dataIndex: "value" },
];

const itemColumns = [
  { dataIndex: "name", title: 'Наименование товара' },
  { dataIndex: "quantity", title: 'Количество' },
  { dataIndex: "price", title: 'Цена', render: (price: number) => <span>{price}&#8381;</span> },
  { dataIndex: "total", title: 'Итого', render: (total: number) => <span>{total}&#8381;</span> },
];

const View: React.FC = () => {
  const { id } = useParams();
  const { data: order, isLoading: fetchLoading } = useFetchOrderQuery(Number(id));

  return (
    <>
      <h1>Заказ {order?.id}</h1>
      <Row gutter={[32, 32]}>
        <Col span={24}>
          <Card title="Общий">
            <Table
              size="small"
              loading={fetchLoading}
              showHeader={false}
              pagination={false}
              columns={baseColumns}
              dataSource={order && [
                {key: 'Номер заказа в 1c', value: order.otherId},
                {key: 'Время заказа', value: order.createdAt.format('DD.MM.YYYY[г.] HH:mm')},
                {key: 'Статус', value: <StatusStep full statuses={order.statuses} paymentType={order.paymentType} deliveryType={order.deliveryType} />},
                {key: 'Тип оплаты / Тип доставки', value: (order.paymentType ? 'Оплата картой' : 'Наличными') + ' / ' + (order.deliveryType ? 'Доставка' : 'Самовывоз')},
                {key: 'Адрес доставки', value: order.deliveryAddress},
                {key: 'Сумма заказа', value: <span>{order.cost}&#8381;</span>},
                {key: 'Аптека', value: order.store.name},
                {key: 'Оплачено', value: order.statuses.some(item => item.value === 'P' && item.state === 2)},
                {key: 'Заказчик', value: order.user.name},
                {key: 'Заметка', value: order.note},
              ]}
            />
          </Card>
        </Col>

        <Col span={24}>
          <Card title="Товары">
            <Table
              size="small"
              loading={fetchLoading}
              pagination={false}
              columns={itemColumns}
              dataSource={order?.items.map(item => ({
                name: item.product.name,
                quantity: item.quantity,
                price: item.price,
                total: item.quantity * item.price
              }))}
            />
          </Card>
        </Col>
      </Row>
    </>
  );
};

export default View;
