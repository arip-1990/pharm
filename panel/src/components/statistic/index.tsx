import React from "react";
import { Button, Card, Col, Row, Table, TablePaginationConfig } from "antd";
import { useFetchStatisticsQuery } from "../../services/StatisticService";
import moment from "moment";
import { useSessionStorage } from "react-use-storage";
import { SortOrder } from "antd/lib/table/interface";

interface StorageType {
  order: { field: string | null; direction: "asc" | "desc" };
  pagination: { current: number; pageSize: number };
}

const Statistic: React.FC = () => {
  const [filters, setFilters] = useSessionStorage<StorageType>(
    "statisticFilters",
    {
      order: { field: null, direction: "asc" },
      pagination: { current: 1, pageSize: 10 },
    }
  );
  const { data: statistics, isLoading: fetchLoading } = useFetchStatisticsQuery(
    filters
  );

  const columns = [
    {
      title: "ip",
      dataIndex: "ip",
      sorter: true,
      sortOrder:
        filters.order.field === "ip"
          ? ((filters.order.direction === "asc"
              ? "ascend"
              : "descend") as SortOrder)
          : null,
    },
    {
      title: "Пользователь",
      dataIndex: "user",
      sorter: true,
      sortOrder:
        filters.order.field === "user"
          ? ((filters.order.direction === "asc"
              ? "ascend"
              : "descend") as SortOrder)
          : null,
    },
    {
      title: "Город",
      dataIndex: "city",
      sorter: true,
      sortOrder:
        filters.order.field === "city"
          ? ((filters.order.direction === "asc"
              ? "ascend"
              : "descend") as SortOrder)
          : null,
    },
    {
      title: "Система",
      dataIndex: "os",
      sorter: true,
      sortOrder:
        filters.order.field === "os"
          ? ((filters.order.direction === "asc"
              ? "ascend"
              : "descend") as SortOrder)
          : null,
    },
    {
      title: "Браузер",
      dataIndex: "browser",
      sorter: true,
      sortOrder:
        filters.order.field === "browser"
          ? ((filters.order.direction === "asc"
              ? "ascend"
              : "descend") as SortOrder)
          : null,
    },
    {
      title: "Зашел на сайт",
      dataIndex: "created_at",
      sorter: true,
      sortOrder:
        filters.order.field === "created_at"
          ? ((filters.order.direction === "asc"
              ? "ascend"
              : "descend") as SortOrder)
          : null,
    },
    {
      title: "На сайте",
      dataIndex: "diff",
    },
  ];

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
      },
    } as StorageType);
  };

  const resetFilters = () => {
    setFilters({ ...filters, order: { field: null, direction: "asc" } });
  };

  return (
    <Row gutter={[16, 16]}>
      <Col span={24}>
        <h2>Статистика посещений</h2>
      </Col>
      <Col span={24}>
        <Card
          title={
            <div style={{ display: "flex", justifyContent: "space-between" }}>
              <span>
                Всего {statistics?.meta.total.toLocaleString("ru") || 0} записи
              </span>
              <Button type="primary" onClick={resetFilters}>
                Сбросить фильтр
              </Button>
            </div>
          }
        >
          <Table
            size="small"
            columns={columns}
            loading={fetchLoading}
            dataSource={statistics?.data.map((item) => ({
              key: item.id,
              ip: item.ip,
              user: item.user?.name,
              city: item.city,
              os: item.os,
              browser: item.browser,
              created_at: item.createdAt.format("DD.MM.YYYY[г.]"),
              diff: moment
                .duration(item.updatedAt.diff(item.createdAt))
                .humanize(),
            }))}
            onChange={handleChange}
            pagination={{
              current:
                statistics?.meta.current_page || filters.pagination.current,
              total: statistics?.meta.total || 0,
              pageSize:
                statistics?.meta.per_page || filters.pagination.pageSize,
              showQuickJumper: true,
            }}
          />
        </Card>
      </Col>
    </Row>
  );
};

export default Statistic;
