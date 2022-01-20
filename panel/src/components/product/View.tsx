import React from "react";
import { Card, Col, Row, Image } from "antd";
import { productApi } from "../../services/ProductService";
import { Table } from "..";
import { useParams } from "react-router-dom";

const generalColumns = [{ dataIndex: "key" }, { dataIndex: "value" }];

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
              <Card title="Общий">
                <Table
                  size="small"
                  showHeader={false}
                  columns={generalColumns}
                  loading={fetchLoading}
                  data={[
                    { key: "Код", value: product?.code },
                    { key: "Штрих-код", value: product?.barcode },
                    { key: "Название", value: product?.name },
                    { key: "Категория", value: product?.category?.name },
                    {
                      key: "Статус",
                      value: product?.status ? "Активен" : "Не активен",
                    },
                  ]}
                />
              </Card>
            </Col>
            <Col span={24}>
              <Card title="Описание">
                <div
                  dangerouslySetInnerHTML={{
                    __html: product?.description || "",
                  }}
                />
              </Card>
            </Col>
          </Row>
        </Col>
        <Col span={12}>
          <Row gutter={[32, 32]}>
            <Col span={24}>
              <Card title="Аттрибуты">
                <Table
                  size="small"
                  showHeader={false}
                  columns={generalColumns}
                  loading={fetchLoading}
                  data={product?.attributes
                    .filter((item) => item.attrubuteType === "string")
                    .map((item) => ({
                      key: item.attrubuteName,
                      value: item.value,
                    }))}
                />
              </Card>
            </Col>
            <Col span={24}>
              <Card title="Дополнительные аттрибуты">
                <Table
                  size="small"
                  showHeader={false}
                  columns={generalColumns}
                  loading={fetchLoading}
                  data={product?.attributes
                    .filter((item) => item.attrubuteType === "text")
                    .map((item) => ({
                      key: item.attrubuteName,
                      value: (
                        <span
                          dangerouslySetInnerHTML={{ __html: item.value }}
                        />
                      ),
                    }))}
                />
              </Card>
            </Col>
          </Row>
        </Col>

        <Col span={24}>
          <Card title="Отографии">
            <Image.PreviewGroup>
              {product?.photos
                .filter((item) => !!item.id)
                .map((item) => (
                  <Image width={200} src={item.url} />
                ))}
            </Image.PreviewGroup>
          </Card>
        </Col>
      </Row>
    </>
  );
};

export default View;
