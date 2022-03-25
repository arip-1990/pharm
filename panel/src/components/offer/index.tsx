import React from "react";
import {useNavigate} from "react-router-dom";
import {Button, Card, Col, Row, Table, TablePaginationConfig} from "antd";
import {useSessionStorage} from "react-use-storage";
import {useFetchOffersQuery} from "../../services/OfferService";

const columns = [
  {
    title: "Код",
    dataIndex: "code",
    sorter: true,
  },
  {
    title: "Наименование",
    dataIndex: "name",
    sorter: true,
  },
  {
    title: "Количество аптек",
    dataIndex: "stores",
  },
];

interface StorageType {
  order: { field: string | null, direction: "asc" | "desc" };
  pagination: { current: number, pageSize: number }
}

const Order: React.FC = () => {
  const [filters, setFilters] = useSessionStorage<StorageType>('offerFilters', {
    order: {field: null, direction: 'asc'},
    pagination: {current: 1, pageSize: 10}
  });
  const {data: offers, isLoading: fetchLoading} = useFetchOffersQuery(filters);
  const navigate = useNavigate();

  const handleChange = (
    pag: TablePaginationConfig,
    filter: any,
    sorter: any
  ) => {
    setFilters({
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
    setFilters({...filters, order: {field: null, direction: 'asc'}});
  }

  return (
    <Row gutter={[16, 16]}>
      <Col span={24}>
        <h2>Остатки</h2>
      </Col>
      <Col span={24}>
        <Card title={
          <div style={{display: 'flex', justifyContent: 'space-between'}}>
            <span>`Всего ${offers?.meta.total.toLocaleString('ru') || 0} записи`</span>
            <Button type='primary' onClick={resetFilters}>Сбросить фильтр</Button>
          </div>
        }>
          <Table
            columns={columns}
            loading={fetchLoading}
            dataSource={offers?.data.map((item) => ({
              key: item.slug,
              code: item.code,
              name: item.name,
              stores: item.items.length,
            }))}
            onChange={handleChange}
            pagination={{
              current: offers?.meta.current_page || filters.pagination.current,
              total: offers?.meta.total || 0,
              pageSize: offers?.meta.per_page || filters.pagination.pageSize,
            }}
            onRow={(record) => ({
              onClick: () => navigate(`/offer/${record.key}`, {state: {menuItem: ['offer']}})
            })}
          />
        </Card>
      </Col>
    </Row>
  );
};

export default Order;
