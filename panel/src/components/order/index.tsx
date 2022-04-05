import React from "react";
import {useNavigate} from "react-router-dom";
import {Button, Card, Col, Row, Table, TablePaginationConfig} from "antd";
import {useFetchOrdersQuery} from "../../services/OrderService";
import StatusStep from "./StatusStep";
import {useSessionStorage} from "react-use-storage";
import {SortOrder} from "antd/lib/table/interface";
import {useFetchUsersQuery} from "../../services/UserService";

interface StorageType {
  order: { field: string | null, direction: "asc" | "desc" };
  filters: { field: string; value: string }[];
  pagination: { current: number, pageSize: number }
}

const Order: React.FC = () => {
  const [filters, setFilters] = useSessionStorage<StorageType>('orderFilters', {
    order: {field: null, direction: 'asc'},
    pagination: {current: 1, pageSize: 10},
    filters: [],
  });
  const {data: users} = useFetchUsersQuery();
  const {data: orders, isLoading: fetchLoading} = useFetchOrdersQuery(filters);
  const navigate = useNavigate();

  const columns = [
    {
      title: "№",
      dataIndex: "id",
      sorter: true,
      sortOrder: filters.order.field === 'id' ? (filters.order.direction === 'asc' ? 'ascend' : 'descend') as SortOrder : null,
    },
    {
      title: "Имя",
      dataIndex: "userName",
      filters: users?.map(item => ({
        text: item.name,
        value: item.id,
      })),
      filteredValue: filters.filters.filter(item => item.field === 'userName').map(item => item.value),
      sorter: true,
      sortOrder: filters.order.field === 'userName' ? (filters.order.direction === 'asc' ? 'ascend' : 'descend') as SortOrder : null,
    },
    {
      title: "Телефон",
      dataIndex: "userPhone",
      width: 120,
      sorter: true,
      sortOrder: filters.order.field === 'userPhone' ? (filters.order.direction === 'asc' ? 'ascend' : 'descend') as SortOrder : null,
    },
    {
      title: "Аптека",
      dataIndex: "store",
      sorter: true,
      sortOrder: filters.order.field === 'store' ? (filters.order.direction === 'asc' ? 'ascend' : 'descend') as SortOrder : null,
    },
    {
      title: "Статус",
      dataIndex: "status",
      width: 640
    },
    {
      title: "Дата",
      dataIndex: "created_at",
      sorter: true,
      sortOrder: filters.order.field === 'created_at' ? (filters.order.direction === 'asc' ? 'ascend' : 'descend') as SortOrder : null,
    },
  ];

  const handleChange = (
    pag: TablePaginationConfig,
    filter: any,
    sorter: any
  ) => {
    const tmp: any = [];
    if (filter) {
      for (const [key, value] of Object.entries<string[] | null>(filter)) {
        if (value?.length) tmp.push({field: key, value: value.pop()});
      }
    }

    setFilters({
      filters: tmp.length ? tmp : filters.filters,
      order: {
        field: sorter.column ? sorter.field : null,
        direction: sorter.column
          ? sorter.order.substring(0, sorter.order.length - 3)
          : filters.order.direction,
      },
      pagination: {
        current: pag.current || filters.pagination.current,
        pageSize: pag.pageSize || filters.pagination.pageSize,
      }
    } as StorageType);
  };

  const resetFilters = () => {
    setFilters({...filters, order: {field: null, direction: 'asc'}, filters: []});
  }

  return (
    <Row gutter={[16, 16]}>
      <Col span={24}>
        <h2>Заказы</h2>
      </Col>
      <Col span={24}>
        <Card title={
          <div style={{display: 'flex', justifyContent: 'space-between'}}>
            <span>Всего {orders?.meta.total.toLocaleString('ru') || 0} записи</span>
            <Button type='primary' onClick={resetFilters}>Сбросить фильтр</Button>
          </div>
        }>
          <Table
            size='small'
            columns={columns}
            loading={fetchLoading}
            dataSource={orders?.data.map((item) => ({
              key: item.id,
              id: item.id,
              userName: item.user.name,
              userPhone: item.user.phone,
              store: item.store.name,
              status: (
                <StatusStep
                  full
                  statuses={item.statuses}
                  paymentType={item.paymentType}
                  deliveryType={item.deliveryType}
                />
              ),
              created_at: item.createdAt.format("DD.MM.YYYY[г.]"),
            }))}
            onChange={handleChange}
            pagination={{
              current: orders?.meta.current_page || filters.pagination.current,
              total: orders?.meta.total || 0,
              pageSize: orders?.meta.per_page || filters.pagination.pageSize,
            }}
            onRow={(record) => ({
              onClick: () => navigate(`/order/${record.id}`, {state: {menuItem: ['order']}})
            })}
          />
        </Card>
      </Col>
    </Row>
  );
};

export default Order;
