import React from 'react';
import {Card, TablePaginationConfig, Input, Space, Button, Image} from 'antd';
import {SearchOutlined} from '@ant-design/icons';
import { productApi } from '../../services/ProductService';
import { Table } from '..';
import { Link } from 'react-router-dom';
import { IProduct } from '../../models/IProduct';

const Product: React.FC = () => {
  const [search, setSeach] = React.useState<{column: string, text: string}>();
  const [filters, setFilters] = React.useState<{field: string, value: string}[]>([]);
  const [order, setOrder] = React.useState<{field: string | null, direction: string}>({field: null, direction: 'asc'});
  const [pagination, setPagination] = React.useState({current: 1, pageSize: 10});
  const {data: products, isLoading: fetchLoading} = productApi.useFetchProductsQuery({pagination, search, filters, order});

  const getColumnSearchProps = (dataIndex: string) => ({
    filterDropdown: ({ setSelectedKeys, selectedKeys, confirm, clearFilters }: any) => (
      <div style={{ padding: 8 }}>
        <Input
          placeholder={`Поиск ${dataIndex}`}
          value={selectedKeys[0]}
          onChange={e => setSelectedKeys(e.target.value ? [e.target.value] : [])}
          onPressEnter={() => handleSearch(selectedKeys, confirm, dataIndex)}
          style={{ marginBottom: 8, display: 'block' }}
        />
        <Space>
          <Button
            type="primary"
            onClick={() => handleSearch(selectedKeys, confirm, dataIndex)}
            icon={<SearchOutlined />}
            size="small"
            style={{ width: 90 }}
          >
            Поиск
          </Button>
          <Button onClick={() => handleReset(clearFilters)} size="small" style={{ width: 90 }}>
            Сбросить
          </Button>
        </Space>
      </div>
    ),
    filterIcon: (filtered: boolean) => <SearchOutlined style={{ color: filtered ? '#1890ff' : undefined }} />,
    onFilter: (value: any, record: any) => record[dataIndex] ? record[dataIndex].toString().toLowerCase().includes(value.toLowerCase()) : '',
  });

  const columns = [
    {
      title: 'Фото',
      dataIndex: 'photo',
      filters: [
        {
          text: 'Присутствует',
          value: 'on',
        },
        {
          text: 'Отсутствует',
          value: 'off',
        },
      ],
      filterMultiple: false,
      render: (url: string) => (
        <Image
          width={120}
          src={url}
        />
      )
    },
    {
      title: 'Код товара',
      dataIndex: 'code',
      sorter: true,
      ...getColumnSearchProps('code')
    },
    {
      title: 'Штрих-код',
      dataIndex: 'barcode',
      sorter: true,
      ...getColumnSearchProps('barcode')
    },
    {
      title: 'Имя',
      dataIndex: 'name',
      sorter: true,
      ...getColumnSearchProps('name'),
      render: (product: IProduct) => <Link to={product.slug}>{product.name}</Link>
    },
    {
      title: 'Категория',
      dataIndex: 'category',
      sorter: true,
      filters: [
        {
          text: 'Присутствует',
          value: 'on',
        },
        {
          text: 'Отсутствует',
          value: 'off',
        },
      ],
      filterMultiple: false,
    },
    {
      title: 'Статус',
      dataIndex: 'status',
      filters: [
        {
          text: 'Активен',
          value: 'on',
        },
        {
          text: 'Не активен',
          value: 'off',
        },
      ],
      filterMultiple: false,
      sorter: true,
    },
  ];

  const handleSearch = (selectedKeys: string[], confirm: any, dataIndex: string) => {
    confirm();
    setSeach({text: selectedKeys[0], column: dataIndex});
  };

  const handleReset = (clearFilters: () => void) => {
    clearFilters();
    setSeach({ column: '', text: '' });
  };

  const handleChange = (pag: TablePaginationConfig, filter: any, sorter: any) => {
    const tmp: any = [];
    for (const [key, value] of Object.entries<string[] | null>(filter)) {
      if (value) tmp.push({field: key, value: value.pop()})
    }
    setFilters(tmp);
    setOrder(item => ({field: sorter.column ? sorter.field : null, direction: sorter.column ? sorter.order.substring(0, sorter.order.length - 3) : item.direction}));
    setPagination(item => ({current: pag.current || item.current, pageSize: pag.pageSize || item.pageSize}));
  }

  return (
    <Card title='Товары'>
      <Table
        columns={columns}
        loading={fetchLoading}
        data={products?.data.map(item => ({
          key: item.id,
          photo: item.photo,
          code: item.code,
          barcode: item.barcode,
          name: item,
          category: item.category?.name,
          status: item.status
        }))}
        onChange={handleChange}
        pagination={{
          current: products?.current || pagination.current,
          total: products?.total || 0,
          pageSize: products?.pageSize || pagination.pageSize
        }}
      />
    </Card>
  )
}

export default Product;
