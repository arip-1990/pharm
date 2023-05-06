import React from "react";
import { Card, TablePaginationConfig, Row, Col } from "antd";
import { useSessionStorage } from "react-use-storage";
import { Table } from "../../components";
import { useFetchStatisticQuery } from "../../services/ProductService";

const Statistic: React.FC = () => {
  const [filters, setFilters] = useSessionStorage<{
    pagination: { current: number; pageSize: number };
  }>("productStatsFilters", { pagination: { current: 1, pageSize: 50 } });
  const { data, isFetching } = useFetchStatisticQuery(filters);

  const columns: any = [
    {
      title: "Пользователь",
      dataIndex: "user",
    },
    {
      title: "Добавлено всего фото",
      dataIndex: "addAllPhotos",
    },
    {
      title: "Изменено всего товаров",
      dataIndex: "editAllProducts",
    },
  ];

  const handleChange = (pag: TablePaginationConfig) => {
    setFilters({
      ...filters,
      pagination: {
        current: pag.current || filters.pagination.current,
        pageSize: pag.pageSize || filters.pagination.pageSize,
      },
    });
  };

  return (
    <Row gutter={[16, 16]}>
      <Col span={24}>
        <h2>Товары</h2>
      </Col>
      <Col span={24}>
        <Card
          title={
            <div style={{ display: "flex", justifyContent: "space-between" }}>
              <span>
                Всего {data?.meta.total.toLocaleString("ru") || 0} записи
              </span>
            </div>
          }
        >
          <Table
            size="small"
            columns={columns}
            loading={isFetching}
            data={data?.data.map((item) => ({
              key: item.user.id,
              user: item.user.name,
              addAllPhotos: item.addTotalPhotos,
              editAllProducts: item.editTotalProducts,
            }))}
            onChange={handleChange}
            pagination={{
              current: data?.meta.current_page || filters.pagination.current,
              total: data?.meta.total || 0,
              pageSize: data?.meta.per_page || filters.pagination.pageSize,
              showQuickJumper: true,
            }}
          />
        </Card>
      </Col>
    </Row>
  );
};

export default Statistic;
