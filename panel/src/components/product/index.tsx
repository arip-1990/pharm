import React from "react";
import { useNavigate } from "react-router-dom";
import { Card, TablePaginationConfig, Input, Space, Button, Image, Tag } from "antd";
import { SearchOutlined } from "@ant-design/icons";
import { useSessionStorage } from "react-use-storage";
import { productApi } from "../../services/ProductService";
import { Table } from "..";
import { SortOrder } from "antd/lib/table/interface";

const Product: React.FC = () => {
  const [value, setValue] = useSessionStorage<{filters: any[], order: any}>('filter', {filters: [], order: {}});
  const [search, setSeach] = React.useState<{ column: string; text: string }>();
  const [filters, setFilters] = React.useState<
    { field: string; value: string }[]
  >([]);
  const [order, setOrder] = React.useState<{
    field: string | null;
    direction: string;
  }>({ field: null, direction: "asc" });
  const [pagination, setPagination] = React.useState({
    current: 1,
    pageSize: 10,
  });
  const {
    data: products,
    isLoading: fetchLoading,
  } = productApi.useFetchProductsQuery({ pagination, search, filters, order });
  const navigate = useNavigate();

  const getColumnSearchProps = (dataIndex: string) => ({
    filterDropdown: ({
      setSelectedKeys,
      selectedKeys,
      confirm,
      clearFilters,
    }: any) => (
      <div style={{ padding: 8 }}>
        <Input
          placeholder={`Поиск ${dataIndex}`}
          value={selectedKeys[0]}
          onChange={(e) =>
            setSelectedKeys(e.target.value ? [e.target.value] : [])
          }
          onPressEnter={() => handleSearch(selectedKeys, confirm, dataIndex)}
          style={{ marginBottom: 8, display: "block" }}
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
          <Button
            onClick={() => handleReset(clearFilters)}
            size="small"
            style={{ width: 90 }}
          >
            Сбросить
          </Button>
        </Space>
      </div>
    ),
    filterIcon: (filtered: boolean) => (
      <SearchOutlined style={{ color: filtered ? "#1890ff" : undefined }} />
    ),
    onFilter: (value: any, record: any) =>
      record[dataIndex]
        ? record[dataIndex]
            .toString()
            .toLowerCase()
            .includes(value.toLowerCase())
        : "",
  });

  const columns = [
    {
      title: "Фото",
      dataIndex: "photo",
      filters: [
        {
          text: "Присутствует",
          value: "on",
        },
        {
          text: "Отсутствует",
          value: "off",
        },
      ],
      defaultFilteredValue: value?.filters.filter(item => item.field === 'photo').map(item => item.value),
      filterMultiple: false,
      render: (url: string) => <Image preview={false} width={120} src={url} />,
    },
    {
      title: "Код товара",
      dataIndex: "code",
      sorter: true,
      defaultSortOrder: value?.order.field === 'code' ? (value.order.direction === 'asc' ? 'ascend' : 'descend') as SortOrder : null,
      ...getColumnSearchProps("code"),
    },
    {
      title: "Штрих-код",
      dataIndex: "barcode",
      sorter: true,
      defaultSortOrder: value?.order.field === 'barcode' ? (value.order.direction === 'asc' ? 'ascend' : 'descend') as SortOrder : null,
      ...getColumnSearchProps("barcode"),
    },
    {
      title: "Название",
      dataIndex: "name",
      sorter: true,
      defaultSortOrder: value?.order.field === 'name' ? (value.order.direction === 'asc' ? 'ascend' : 'descend') as SortOrder : null,
      ...getColumnSearchProps("name"),
    },
    {
      title: "Категория",
      dataIndex: "category",
      sorter: true,
      filters: [
        {
          text: "Присутствует",
          value: "on",
        },
        {
          text: "Отсутствует",
          value: "off",
        },
      ],
      defaultFilteredValue: value?.filters.filter(item => item.field === 'category').map(item => item.value),
      defaultSortOrder: value?.order.field === 'category' ? (value.order.direction === 'asc' ? 'ascend' : 'descend') as SortOrder : null,
      filterMultiple: false,
    },
    {
      title: "Статус",
      dataIndex: "status",
      filters: [
        {
          text: "Активен",
          value: "on",
        },
        {
          text: "Не активен",
          value: "off",
        },
      ],
      defaultFilteredValue: value?.filters.filter(item => item.field === 'status').map(item => item.value),
      filterMultiple: false,
      sorter: true,
      defaultSortOrder: value?.order.field === 'status' ? (value.order.direction === 'asc' ? 'ascend' : 'descend') as SortOrder : null,
      render: (status: boolean) => status ? <Tag color="green">Активен</Tag> : <Tag color="red">Не активен</Tag>
    },
  ];

  React.useEffect(() => {
    if (value) {
      setFilters(value.filters);
      setOrder(value.order);
    }
  }, []);

  const handleSearch = (
    selectedKeys: string[],
    confirm: any,
    dataIndex: string
  ) => {
    confirm();
    setSeach({ text: selectedKeys[0], column: dataIndex });
  };

  const handleReset = (clearFilters: () => void) => {
    clearFilters();
    setSeach({ column: "", text: "" });
  };

  const handleChange = (
    pag: TablePaginationConfig,
    filter: any,
    sorter: any
  ) => {
    if (filter) {
      const tmp: any = [];
      for (const [key, value] of Object.entries<string[] | null>(filter)) {
        if (value?.length) tmp.push({ field: key, value: value.pop() });
      }
      setFilters(tmp);
      setValue({
        filters: tmp,
        order: {
          field: sorter.column ? sorter.field : null,
          direction: sorter.column
            ? sorter.order.substring(0, sorter.order.length - 3)
            : value.order.direction,
        }
      });
    }

    if (sorter) {
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
    }
  };

  return (
    <Card title="Товары">
      <Table
        columns={columns}
        loading={fetchLoading}
        data={products?.data.map((item) => ({
          key: item.slug,
          photo: item.photos[0].url,
          code: item.code,
          barcode: item.barcode,
          name: item.name,
          category: item.category?.name,
          status: item.status,
        }))}
        onChange={handleChange}
        pagination={{
          current: products?.meta.current_page || pagination.current,
          total: products?.meta.total || 0,
          pageSize: products?.meta.per_page || pagination.pageSize,
        }}
        onRow={(record) => ({
          onClick: () => navigate(`/product/${record.key}`)
        })}
      />
    </Card>
  );
};

export default Product;
