import React from "react";
import { useParams } from "react-router-dom";
import { Card, Col, Row, Image, Space } from "antd";
import { productApi } from "../../services/ProductService";
import { ViewBase } from "./ViewBase";
import { ViewDescription } from "./ViewDescription";
import { ViewAttributes } from "./ViewAttributes";
import { IProduct } from "../../models/IProduct";

const View: React.FC = () => {
  const { slug } = useParams();
  const {
    data: product,
    isLoading: fetchLoading,
  } = productApi.useFetchProductQuery(slug || "", { skip: !slug });

  return (
    <>
      <h1>{product?.name}</h1>
      <Row gutter={[32, 32]}>
        <Col span={12}>
          <Row gutter={[32, 32]}>
            <Col span={24}>
              <ViewBase loading={fetchLoading} product={product} />
            </Col>
            <Col span={24}>
              <ViewDescription loading={fetchLoading} product={product} />
            </Col>
          </Row>
        </Col>
        <Col span={12}>
          <Row gutter={[32, 32]}>
            <Col span={24}>
              <ViewAttributes loading={fetchLoading} product={product} />
            </Col>
          </Row>
        </Col>

        <Col span={24}>
          <Card title="Отографии">
            <Image.PreviewGroup>
              <Space>
                {product?.photos
                  .filter((item) => !!item.id)
                  .map((item) => (
                    <Image key={item.id} width={200} src={item.url} />
                ))}
              </Space>
            </Image.PreviewGroup>
          </Card>
        </Col>
      </Row>
    </>
  );
};

export default View;
