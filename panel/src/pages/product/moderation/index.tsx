import {
  Row,
  Col,
  Card,
  Button,
  Image,
  Space,
  Typography,
  Modal,
  Table,
} from "antd";
import { CheckOutlined, CloseOutlined } from "@ant-design/icons";
import React from "react";
import {
  useFetchModerationProductsQuery,
  useUpdateModerationProductMutation,
} from "../../../services/ProductService";
import { IProduct } from "../../../models/IProduct";

const attributeColumns = [
  { dataIndex: "key", width: 136 },
  { dataIndex: "type", colSpan: 0, render: () => undefined },
  {
    dataIndex: "value",
    render: (item: any) =>
      item.type === "text" ? (
        <div dangerouslySetInnerHTML={{ __html: item.value || "" }} />
      ) : (
        item.value
      ),
  },
];

const Moderation: React.FC = () => {
  const { data: products, isLoading } = useFetchModerationProductsQuery();
  const [updateProduct] = useUpdateModerationProductMutation();

  const handleUpdate = (slug: string, check: boolean) => {
    updateProduct({ slug, check });
  };

  const getAttributes = (product: IProduct | undefined) => {
    let data: any = [];
    product?.attributes.forEach((item) => {
      data.push({
        key: item.name,
        type: item.type,
        value: item,
      });
    });

    return data;
  };

  const info = (productId: string) => {
    Modal.info({
      title: "Аттрибуты",
      width: 1000,
      content: (
        <Table
          size="small"
          showHeader={false}
          columns={attributeColumns}
          dataSource={getAttributes(
            products?.filter((item) => item.id === productId)[0]
          )}
        />
      ),
      onOk() {},
    });
  };

  return (
    <Row gutter={[16, 16]}>
      <Col span={24}>
        <h2>Модерация товаров</h2>
      </Col>
      <Col span={24}>
        <Card
          title={
            <div style={{ display: "flex", justifyContent: "space-between" }}>
              <span>
                Всего {products?.length.toLocaleString("ru") || 0} записи
              </span>
            </div>
          }
          loading={isLoading}
        >
          {products?.map((product) => {
            const vendor = product.attributes
              .filter((item) => item.id === 1)
              .pop();

            return (
              <Card
                key={product.id}
                title={
                  <Row>
                    <Col span={2}>{product.code}</Col>
                    <Col span={4}>
                      {vendor ? (
                        <Typography.Text type="success">
                          {vendor.value}
                        </Typography.Text>
                      ) : (
                        <Typography.Text type="danger">
                          Производителя нет!
                        </Typography.Text>
                      )}
                    </Col>
                    <Col span={16}>
                      <Typography.Text type="success">
                        {product.name}
                      </Typography.Text>
                    </Col>
                    <Col span={2} style={{ textAlign: "end" }}>
                      <Space>
                        <Button
                          type="primary"
                          size="small"
                          onClick={() => handleUpdate(product.slug, true)}
                        >
                          <CheckOutlined />
                        </Button>
                        <Button
                          type="primary"
                          size="small"
                          danger
                          onClick={() => handleUpdate(product.slug, false)}
                        >
                          <CloseOutlined />
                        </Button>
                      </Space>
                    </Col>
                  </Row>
                }
                style={{ marginBottom: "1rem" }}
              >
                <Row style={{ alignItems: "center" }}>
                  <Col span={2}>
                    <Image width={64} src={product.photos[0].url} />
                  </Col>
                  <Col span={20}>
                    <Typography.Text
                      type="secondary"
                      style={{ fontSize: "0.75rem" }}
                    >
                      Описание:
                    </Typography.Text>
                    <div
                      dangerouslySetInnerHTML={{
                        __html: product.description || "",
                      }}
                    />
                  </Col>
                  <Col span={2}>
                    <Typography.Text
                      type="success"
                      style={{ cursor: "pointer" }}
                      onClick={() => info(product.id)}
                    >
                      Показать аттрибуты
                    </Typography.Text>
                  </Col>
                </Row>
              </Card>
            );
          })}
        </Card>
      </Col>
    </Row>
  );
};

export default Moderation;
