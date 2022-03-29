import React from "react";
import {useNavigate} from "react-router-dom";
import {Button, Card, Col, Input, Row, Space, Table, TablePaginationConfig} from "antd";
import {useSessionStorage} from "react-use-storage";
import {useFetchOffersQuery} from "../../services/OfferService";
import {SortOrder} from "antd/lib/table/interface";
import {SearchOutlined} from "@ant-design/icons";

interface StorageType {
  search: { column: string; text: string };
  order: { field: string | null, direction: "asc" | "desc" };
  pagination: { current: number, pageSize: number }
}

const Order: React.FC = () => {
  const [filters, setFilters] = useSessionStorage<StorageType>('offerFilters', {
    search: {column: '', text: ''},
    order: {field: null, direction: 'asc'},
    pagination: {current: 1, pageSize: 10}
  });
  const {data: offers, isLoading: fetchLoading} = useFetchOffersQuery(filters);
  const navigate = useNavigate();

  const getColumnSearchProps = (dataIndex: string) => ({
    filterDropdown: ({setSelectedKeys, selectedKeys, confirm, clearFilters}: any) => (
      <div style={{padding: 8}}>
        <Input
          placeholder={`Поиск ${dataIndex}`}
          defaultValue={filters.search.text}
          value={selectedKeys[0]}
          onChange={e => setSelectedKeys(e.target.value ? [e.target.value] : [])}
          onPressEnter={() => handleSearch(selectedKeys, confirm, dataIndex)}
          style={{marginBottom: 8, display: "block"}}
        />
        <Space>
          <Button
            type="primary"
            onClick={() => handleSearch(selectedKeys, confirm, dataIndex)}
            icon={<SearchOutlined/>}
            size="small"
            style={{width: 90}}
          >
            Поиск
          </Button>
          <Button
            onClick={() => handleReset(clearFilters, confirm)}
            size="small"
            style={{width: 90}}
          >
            Сбросить
          </Button>
        </Space>
      </div>
    ),
    filterIcon: (filtered: boolean) => (
      <SearchOutlined style={{color: filtered ? "#1890ff" : undefined}}/>
    )
  });

  const columns = [
    {
      title: "Код",
      dataIndex: "code",
      width: 120,
      sorter: true,
      sortOrder: filters.order.field === 'code' ? (filters.order.direction === 'asc' ? 'ascend' : 'descend') as SortOrder : null,
    },
    {
      title: "Наименование",
      dataIndex: "name",
      sorter: true,
      sortOrder: filters.order.field === 'name' ? (filters.order.direction === 'asc' ? 'ascend' : 'descend') as SortOrder : null,
      ...getColumnSearchProps("name"),
    },
    {
      title: "Количество аптек",
      dataIndex: "stores",
      width: 160,
    },
  ];

  const handleSearch = (
    selectedKeys: string[],
    confirm: () => void,
    dataIndex: string
  ) => {
    confirm();
    selectedKeys[0] && setFilters({...filters, search: {text: selectedKeys[0], column: dataIndex}});
  };

  const handleReset = (clearFilters: () => void, confirm: () => void) => {
    clearFilters();
    confirm();
    setFilters({...filters, search: {column: "", text: ""}});
  };

  const handleChange = (pag: TablePaginationConfig, filter: any, sorter: any) => {
    setFilters({
      ...filters,
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
    setFilters({
      ...filters,
      search: {column: '', text: ''},
      order: {field: null, direction: 'asc'}
    });
  }

  return (
    <Row gutter={[16, 16]}>
      <Col span={24}>
        <h2>Остатки</h2>
      </Col>
      <Col span={24}>
        <Card title={
          <div style={{display: 'flex', justifyContent: 'space-between'}}>
            <span>Всего {offers?.meta.total.toLocaleString('ru') || 0} записи</span>
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
