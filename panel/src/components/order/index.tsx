import React from "react";
import { useNavigate } from "react-router-dom";
import { Card, Table, TablePaginationConfig } from "antd";
import { orderApi } from "../../services/OrderService";
import StatusStep from "./StatusStep";

const columns = [
  {
    title: "№",
    dataIndex: "id",
    sorter: true,
  },
  {
    title: "Пользователь",
    dataIndex: "user",
    sorter: true,
  },
  {
    title: "Аптека",
    dataIndex: "store",
    sorter: true,
  },
  {
    title: "Сумма заказа",
    dataIndex: "cost",
    render: (cost: number) => <span>{cost}&#8381;</span>,
  },
  {
    title: "Статус",
    dataIndex: "status",
  },
  {
    title: "Дата",
    dataIndex: "createdAt",
    sorter: true,
  },
];

const Order: React.FC = () => {
  const [pagination, setPagination] = React.useState({
    current: 1,
    pageSize: 10,
  });
  const [order, setOrder] = React.useState<{
    field: string | null;
    direction: string;
  }>({ field: null, direction: "asc" });
  const {
    data: orders,
    isLoading: fetchLoading,
  } = orderApi.useFetchOrdersQuery({ pagination, order });
  const navigate = useNavigate();

  const handleChange = (
    pag: TablePaginationConfig,
    filter: any,
    sorter: any
  ) => {
    setOrder((item) => ({
      field: sorter.column ? sorter.field : null,
      direction: sorter.column
        ? sorter.order.substring(0, sorter.order.length - 3)
        : item.direction,
    }));
    setPagination((item) => ({
      current: pag.current || item.current,
      pageSize: pag.pageSize || item.pageSize,
    }));
  };

  return (
    <Card title="Заказы">
      <Table
        columns={columns}
        loading={fetchLoading}
        dataSource={orders?.data.map((item) => ({
          key: item.id,
          id: item.id,
          user: item.user.name,
          store: item.store.name,
          cost: item.cost,
          status: (
            <StatusStep
              statuses={item.statuses}
              paymentType={item.paymentType}
              deliveryType={item.deliveryType}
            />
          ),
          createdAt: item.createdAt.format("DD.MM.YYYY[г.]"),
        }))}
        onChange={handleChange}
        pagination={{
          current: orders?.meta.current_page || pagination.current,
          total: orders?.meta.total || 0,
          pageSize: orders?.meta.per_page || pagination.pageSize,
        }}
        onRow={(record) => ({
          onClick: () => navigate(`/order/${record.id}`)
        })}
      />
    </Card>
  );
};

export default Order;
