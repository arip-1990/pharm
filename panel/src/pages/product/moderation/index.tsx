import { Row, Col, Card, Button, Image, Space } from "antd";
import { CheckOutlined, CloseOutlined } from "@ant-design/icons";
import React from "react";
import {
  useFetchModerationProductsQuery,
  useUpdateModerationProductMutation,
} from "../../../services/ProductService";

const Moderation: React.FC = () => {
  const { data: products, isLoading } = useFetchModerationProductsQuery();
  const [updateProduct] = useUpdateModerationProductMutation();

  const handleUpdate = (slug: string, check: boolean) => {
    updateProduct({ slug, check });
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
          {products?.map((product) => (
            <Card
              key={product.id}
              title={
                <Row>
                  <Col span={4}>{product.code}</Col>
                  <Col span={20}>{product.name}</Col>
                </Row>
              }
              style={{ marginBottom: "1rem" }}
            >
              <Row style={{ alignItems: "center" }}>
                <Col span={4}>
                  <Image width={64} src={product.photos[0].url} />
                </Col>
                <Col span={18}>
                  <div
                    dangerouslySetInnerHTML={{
                      __html: product.description || "",
                    }}
                  />
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
            </Card>
          ))}
        </Card>
      </Col>
    </Row>
  );
};

export default Moderation;
