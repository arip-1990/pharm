import React from 'react';
import {Card, Col, Row, Table, TablePaginationConfig} from 'antd';
import { useFetchStatisticsQuery } from '../../services/StatisticService';
import moment from 'moment';

const columns = [
  {
    title: 'ip',
    dataIndex: 'ip',
    sorter: true,
  },
  {
    title: 'Город',
    dataIndex: 'city',
    sorter: true,
  },
  {
    title: 'Система',
    dataIndex: 'os',
    sorter: true,
  },
  {
    title: 'Браузер',
    dataIndex: 'browser',
    sorter: true,
  },
  {
    title: 'Зашел на сайт',
    dataIndex: 'created_at',
    sorter: true,
  },
  {
    title: 'На сайте',
    dataIndex: 'diff',
  },
];

const Statistic: React.FC = () => {
  const [pagination, setPagination] = React.useState({current: 1, pageSize: 10});
  const [order, setOrder] = React.useState<{field: string | null, direction: string}>({field: null, direction: 'asc'});
  const {data: statistics, isLoading: fetchLoading} = useFetchStatisticsQuery({pagination, order});

  const handleChange = (pag: TablePaginationConfig, filter: any, sorter: any) => {
    setOrder(item => ({field: sorter.column ? sorter.field : null, direction: sorter.column ? sorter.order.substring(0, sorter.order.length - 3) : item.direction}));
    setPagination(item => ({current: pag.current || item.current, pageSize: pag.pageSize || item.pageSize}));
  }

  return (
    <Row gutter={[16, 16]}>
      <Col span={24}>
        <h2>Статистика посещений</h2>
      </Col>
      <Col span={24}>
        <Card title={`Всего ${statistics?.meta.total || 0} записи`}>
          <Table
          columns={columns}
          loading={fetchLoading}
          dataSource={statistics?.data.map(item => ({
            key: item.id,
            ip: item.ip,
            city: item.city,
            os: item.os,
            browser: item.browser,
            created_at: item.createdAt.format('DD.MM.YYYY[г.]'),
            diff: moment.duration(item.updatedAt.diff(item.createdAt)).humanize()
          }))}
          onChange={handleChange}
          pagination={{
            current: statistics?.meta.current_page || pagination.current,
            total: statistics?.meta.total || 0,
            pageSize: statistics?.meta.per_page || pagination.pageSize
          }}
        />
      </Card>
      </Col>
    </Row>
  )
}

export default Statistic;
