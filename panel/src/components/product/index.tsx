import React, {useState} from "react";
import { Link } from "react-router-dom";
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
  Typography, Modal, InputNumber,
} from "antd";
import {FileExcelOutlined, SearchOutlined} from "@ant-design/icons";
import { useSessionStorage } from "react-use-storage";
import { useFetchProductsQuery } from "../../services/ProductService";
import { Table } from "..";
import { SortOrder } from "antd/lib/table/interface";
import {API_URL} from "../../services/api";

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
      pagination: { current: 1, pageSize: 50 },
    }
  );
  const { data: products, isFetching } = useFetchProductsQuery(filters);
  const [searchText, setSearchText] = React.useState<string>();

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
      render: (item: string, record: any) => (
        <Link to={`/products/${record.key}`} state={{ menuItem: "products" }}>
          {item}
        </Link>
      ),
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
      title: "Наличие",
      dataIndex: "offer",
      filters: [
        {
          text: "Есть",
          value: "on",
        },
        {
          text: "Нет",
          value: "off",
        },
      ],
      filteredValue: filters.filters
        .filter((item) => item.field === "offer")
        .map((item) => item.value),
      filterMultiple: false,
      render: (offers: number) =>
        offers ? <Tag color="green">Есть</Tag> : <Tag color="red">Нет</Tag>,
    },
  ];

  const handleSearch = (dataIndex: string, confirm: () => void) => {
    searchText &&
      setFilters({
        ...filters,
        pagination: {
          ...filters.pagination,
          current: 1,
        },
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


  const [isModalOpen, setIsModalOpen] = useState(false);
  const [page, setPage] = useState<number|null|string>()

  const showModal = () => {
    setIsModalOpen(true);
  };

  const handleOk = () => {
    const url = `${API_URL}/v1/panel/product/export/product/not-photo?page=${page}`;
    // Формируем ссылку
    if (page == null) {
      alert("Введите только цифры")
    }else {
      // Создаём скрытую ссылку и кликаем по ней
      const link = document.createElement("a");
      link.href = url;
      link.setAttribute("download", `products_without_photos_page_${page}.xlsx`);
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    }

    setIsModalOpen(false);
  };

  const handleCancel = () => {
    setIsModalOpen(false);
  };

  return (
    <Row gutter={[16, 16]}>
      <Col span={24}>
        <h2>Товары</h2>
      </Col>
      <Col span={24}>
        <Modal title="Выгрузка товаров без фото в Exel"
               open={isModalOpen}
               onOk={handleOk}
               onCancel={handleCancel}
        >
          <p>Введите номер страницы(число) одна страница содержит 1000 уникальных записей </p>
          <InputNumber
            min={1}
            style={{ width: "100%" }}
            value={page}
            onChange={(value) => setPage(value)}
          />
        </Modal>
        <Card
          title={
            <div style={{ display: "flex", justifyContent: "space-between" }}>
              <span>
                Всего {products?.meta.total.toLocaleString("ru") || 0} записи
              </span>
              <div>
                <Button type="primary" onClick={resetFilters} >
                  Сбросить фильтр
                </Button>
                <FileExcelOutlined style={{cursor:"pointer"}} onClick={showModal} />
              </div>
            </div>
          }
        >
          <Table
            size="small"
            columns={columns}
            loading={isFetching}
            data={products?.data.map((item) => ({
              key: item.slug,
              photo: { url: item.photos[0]?.url, total: item.photos?.length },
              sale: item.sale,
              code: item.code,
              barcode: item.barcodes.join(", "),
              name: item.name,
              category: item.category?.name,
              offer: item.offers.length,
            }))}
            onChange={handleChange}
            pagination={{
              current:
                products?.meta.current_page || filters.pagination.current,
              total: products?.meta.total || 0,
              pageSize: products?.meta.per_page || filters.pagination.pageSize,
              showQuickJumper: true,
            }}
          />
        </Card>
      </Col>
    </Row>
  );
};

export default Product;
