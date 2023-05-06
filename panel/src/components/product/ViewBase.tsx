import React from "react";
import {
  Card,
  Input,
  Switch,
  Select,
  TreeSelect,
  Form,
  Space,
  Tag,
} from "antd";
import { EditOutlined, CheckOutlined, CloseOutlined } from "@ant-design/icons";
import { Table } from "..";
import { IProduct } from "../../models/IProduct";
import { categoryApi } from "../../services/CategoryService";
import { ICategory } from "../../models/ICategory";
import { useUpdateProductMutation } from "../../services/ProductService";

interface PropsType {
  product?: IProduct;
  loading: boolean;
}

const ViewBase: React.FC<PropsType> = ({ product, loading }) => {
  const [edit, setEdit] = React.useState<boolean>(false);
  const { data: categories } = categoryApi.useFetchCategoriesQuery();
  const [updateProduct] = useUpdateProductMutation();
  const [form] = Form.useForm();

  const getCategoryTree: any = (categories: ICategory[]) =>
    categories?.map((item) => ({
      title: item.name,
      value: item.id,
      children: getCategoryTree(item.children),
    }));

  const generalColumns = [
    { dataIndex: "key", width: 136 },
    {
      dataIndex: "value",
      render: (item: any, record: { key: string; value: string | number }) => {
        switch (record.key) {
          case "Штрих-код":
            return edit ? (
              <Form.Item
                style={{ margin: 0 }}
                name="barcode"
                initialValue={item}
              >
                <Input />
              </Form.Item>
            ) : (
              item
            );
          case "Название":
            return edit ? (
              <Form.Item style={{ margin: 0 }} name="name" initialValue={item}>
                <Input />
              </Form.Item>
            ) : (
              item
            );
          case "Рецептурный":
            return edit ? (
              <Form.Item
                style={{ margin: 0 }}
                name="recipe"
                initialValue={item}
                valuePropName="checked"
              >
                <Switch />
              </Form.Item>
            ) : item ? (
              <Tag color="green">Да</Tag>
            ) : (
              <Tag color="red">Нет</Tag>
            );
          case "Маркирован":
            return edit ? (
              <Form.Item
                style={{ margin: 0 }}
                name="marked"
                initialValue={item}
                valuePropName="checked"
              >
                <Switch />
              </Form.Item>
            ) : item ? (
              <Tag color="green">Да</Tag>
            ) : (
              <Tag color="red">Нет</Tag>
            );
          case "Распродажа":
            return edit ? (
              <Form.Item
                style={{ margin: 0 }}
                name="sale"
                initialValue={item}
                valuePropName="checked"
              >
                <Switch />
              </Form.Item>
            ) : item ? (
              <Tag color="green">Да</Tag>
            ) : (
              <Tag color="red">Нет</Tag>
            );
          case "Статус":
            return edit ? (
              <Form.Item
                style={{ margin: 0 }}
                name="status"
                initialValue={item}
              >
                <Select style={{ width: 120 }}>
                  <Select.Option value={1}>Активен</Select.Option>
                  <Select.Option value={0}>Черновик</Select.Option>
                  <Select.Option value={2}>Модерация</Select.Option>
                </Select>
              </Form.Item>
            ) : item === 2 ? (
              <Tag color="orange">Модерация</Tag>
            ) : item === 1 ? (
              <Tag color="green">Активен</Tag>
            ) : (
              <Tag color="red">Не активен</Tag>
            );
          case "Категория":
            return edit ? (
              <Form.Item
                style={{ margin: 0 }}
                name="category"
                initialValue={item?.id}
              >
                <TreeSelect
                  treeLine={{ showLeafIcon: false }}
                  treeData={getCategoryTree(categories)}
                />
              </Form.Item>
            ) : (
              item?.name
            );
          default:
            return item;
        }
      },
    },
  ];

  const handleSave = async () => {
    let data = await form.validateFields();
    if (product) updateProduct({ slug: product.slug, data });
    setEdit(false);
  };

  const handleReset = () => {
    setEdit(false);
    form.resetFields();
  };

  return (
    <Card
      title="Общий"
      loading={loading}
      extra={
        edit ? (
          <Space>
            <CheckOutlined style={{ color: "#52c41a" }} onClick={handleSave} />
            <CloseOutlined style={{ color: "#ff4d4f" }} onClick={handleReset} />
          </Space>
        ) : (
          <EditOutlined
            style={{ color: "#1890ff" }}
            onClick={() => setEdit(true)}
          />
        )
      }
    >
      <Form component={false} form={form}>
        <Table
          size="small"
          showHeader={false}
          columns={generalColumns}
          data={[
            { key: "Код", value: product?.code },
            { key: "Штрих-код", value: product?.barcodes },
            { key: "Название", value: product?.name },
            { key: "Категория", value: product?.category },
            { key: "Рецептурный", value: product?.recipe },
            { key: "Маркирован", value: product?.marked },
            { key: "Распродажа", value: product?.sale },
            { key: "Статус", value: product?.status },
          ]}
        />
      </Form>
    </Card>
  );
};

export { ViewBase };
