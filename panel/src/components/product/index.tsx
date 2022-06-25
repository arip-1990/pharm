import React from "react";
import { useNavigate } from "react-router-dom";
import {
  Card,
  TablePaginationConfig,
  Input,
  Space,
  Button,
  Image,
  Tag,
  Row,
  Col,
  Typography,
} from "antd";
import { SearchOutlined } from "@ant-design/icons";
import { useSessionStorage } from "react-use-storage";
import { useFetchProductsQuery } from "../../services/ProductService";
import { Table } from "..";
import { SortOrder } from "antd/lib/table/interface";

interface StorageType {
  search: { column: string; text: string };
  order: { field: string | null; direction: "asc" | "desc" };
  filters: { field: string; value: string }[];
  pagination: { current: number; pageSize: number };
}

const Product: React.FC = () => {
  const [filters, setFilters] = useSessionStorage<StorageType>(
    "productFilters",
    {
      search: { column: "", text: "" },
      order: { field: null, direction: "asc" },
      filters: [],
      pagination: { current: 1, pageSize: 10 },
    }
  );
  const { data: products, isLoading: fetchLoading } = useFetchProductsQuery(
    filters
  );
  const [searchText, setSearchText] = React.useState<string>();
  const navigate = useNavigate();

  const getColumnSearchProps = (dataIndex: string) => ({
    filterDropdown: ({ confirm, clearFilters }: any) => (
      <div style={{ padding: 8 }}>
        <Input
          placeholder={`Поиск ${dataIndex}`}
          value={searchText || filters.search.text}
          onChange={(e) =>
            setSearchText(e.target.value ? e.target.value : undefined)
          }
          onPressEnter={() => handleSearch(dataIndex, confirm)}
          style={{ marginBottom: 8, display: "block" }}
        />
        <Space>
          <Button
            type="primary"
            onClick={() => handleSearch(dataIndex, confirm)}
            icon={<SearchOutlined />}
            size="small"
            style={{ width: 90 }}
          >
            Поиск
          </Button>
          <Button
            onClick={() => handleReset(clearFilters, confirm)}
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
  });

  const columns: any = [
    {
      title: "Фото",
      dataIndex: "photo",
      filters: [
        {
          text: "Присутствует",
          value: "present",
        },
        {
          text: "Отсутствует",
          value: "missing",
        },
        {
          text: "Проверенные",
          value: "checked",
        },
        {
          text: "Не проверенные",
          value: "unchecked",
        },
      ],
      filteredValue: filters.filters
        .filter((item) => item.field === "photo")
        .map((item) => item.value),
      filterMultiple: false,
      render: (data: { url: string; total: number }) => (
        <>
          <Image preview={false} width={120} src={data.url} />
          <Typography.Text
            type="secondary"
            style={{ display: "block", textAlign: "center" }}
          >
            (Кол-во: {data.total})
          </Typography.Text>
        </>
      ),
    },
    {
      title: "Распродажа",
      dataIndex: "sale",
      filters: [
        {
          text: "Да",
          value: "on",
        },
        {
          text: "Нет",
          value: "off",
        },
      ],
      filteredValue: filters.filters
        .filter((item) => item.field === "sale")
        .map((item) => item.value),
      filterMultiple: false,
      render: (sale: boolean) => (
        <div style={{ textAlign: "center" }}>
          {sale ? <Tag color="green">Да</Tag> : <Tag color="red">Нет</Tag>}
        </div>
      ),
    },
    {
      title: "Код товара",
      dataIndex: "code",
      sorter: true,
      sortOrder:
        filters.order.field === "code"
          ? ((filters.order.direction === "asc"
              ? "ascend"
              : "descend") as SortOrder)
          : null,
      ...getColumnSearchProps("code"),
    },
    {
      title: "Штрих-код",
      dataIndex: "barcode",
      sorter: true,
      sortOrder:
        filters.order.field === "barcode"
          ? ((filters.order.direction === "asc"
              ? "ascend"
              : "descend") as SortOrder)
          : null,
      ...getColumnSearchProps("barcode"),
    },
    {
      title: "Название",
      dataIndex: "name",
      sorter: true,
      sortOrder:
        filters.order.field === "name"
          ? ((filters.order.direction === "asc"
              ? "ascend"
              : "descend") as SortOrder)
          : null,
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
      filteredValue: filters.filters
        .filter((item) => item.field === "category")
        .map((item) => item.value),
      sortOrder:
        filters.order.field === "category"
          ? ((filters.order.direction === "asc"
              ? "ascend"
              : "descend") as SortOrder)
          : null,
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
      filteredValue: filters.filters
        .filter((item) => item.field === "status")
        .map((item) => item.value),
      filterMultiple: false,
      sorter: true,
      sortOrder:
        filters.order.field === "status"
          ? ((filters.order.direction === "asc"
              ? "ascend"
              : "descend") as SortOrder)
          : null,
      render: (status: boolean) =>
        status ? (
          <Tag color="green">Активен</Tag>
        ) : (
          <Tag color="red">Не активен</Tag>
        ),
    },
  ];

  const handleSearch = (dataIndex: string, confirm: () => void) => {
    searchText &&
      setFilters({
        ...filters,
        search: { text: searchText, column: dataIndex },
      });
    confirm();
  };

  const handleReset = (clearFilters: () => void, confirm: () => void) => {
    clearFilters();
    setSearchText(undefined);
    setFilters({ ...filters, search: { column: "", text: "" } });
    confirm();
  };

  const handleChange = (
    pag: TablePaginationConfig,
    filter: any,
    sorter: any
  ) => {
    const tmp: any = [];
    if (filter) {
      for (const [key, value] of Object.entries<string[] | null>(filter)) {
        if (value?.length) tmp.push({ field: key, value: value.pop() });
      }
    }

    setFilters({
      ...filters,
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
      },
    } as StorageType);
  };

  const resetFilters = () => {
    setSearchText(undefined);
    setFilters({
      ...filters,
      search: { column: "", text: "" },
      order: { field: null, direction: "asc" },
      filters: [],
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
                Всего {products?.meta.total.toLocaleString("ru") || 0} записи
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
            data={products?.data.map((item) => ({
              key: item.slug,
              photo: { url: item.photos[0].url, total: item.photos.length },
              sale: item.sale,
              code: item.code,
              barcode: item.barcode,
              name: item.name,
              category: item.category?.name,
              status: item.status,
            }))}
            onChange={handleChange}
            pagination={{
              current:
                products?.meta.current_page || filters.pagination.current,
              total: products?.meta.total || 0,
              pageSize: products?.meta.per_page || filters.pagination.pageSize,
              showQuickJumper: true,
            }}
            onRow={(record) => ({
              onClick: () =>
                navigate(`/product/${record.key}`, {
                  state: { menuItem: ["product"] },
                }),
            })}
          />
        </Card>
      </Col>
    </Row>
  );
};

export default Product;
