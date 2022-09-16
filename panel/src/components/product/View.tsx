import React from "react";
import { useParams } from "react-router-dom";
import { Col, Row, Card, Table } from "antd";
import { useFetchProductQuery } from "../../services/ProductService";
import { ViewBase } from "./ViewBase";
import { ViewPhotos } from "./ViewPhotos";
import { ViewDescription } from "./ViewDescription";
import { ViewAttributes } from "./ViewAttributes";
import { useFetchOfferQuery } from "../../services/OfferService";

const offerColumns = [
  { dataIndex: "name", title: "Название аптеки" },
  {
    dataIndex: "price",
    title: "Цена",
    render: (price: number) => <span>{price}&#8381;</span>,
  },
  { dataIndex: "quantity", title: "Количество" },
];

const View: React.FC = () => {
  const { slug } = useParams();
  const {
    data: product,
    isFetching: productLoading,
  } = useFetchProductQuery(slug || "", { skip: !slug });
  // const { data: offers, isFetching: offersLoading } = useFetchOfferQuery(
  //   slug || "",
  //   {
  //     skip: !slug,
  //   }
  // );

  return (
    <Row gutter={[32, 32]}>
      <Col span={24}>
        <h2 style={{ margin: 0 }}>{product?.name}</h2>
      </Col>

      <Col span={12}>
        <Row gutter={[32, 32]}>
          <Col span={24}>
            <ViewBase loading={productLoading} product={product} />
          </Col>
          <Col span={24}>
            <ViewDescription loading={productLoading} product={product} />
          </Col>
        </Row>
      </Col>

      <Col span={12}>
        <Row gutter={[32, 32]}>
          <Col span={24}>
            <ViewAttributes loading={productLoading} product={product} />
          </Col>
        </Row>
      </Col>

      <Col span={24}>
        <ViewPhotos
          slug={slug || ""}
          photos={product?.photos || []}
          loading={productLoading}
        />
      </Col>

      <Col span={24}>
        <Card title="Аптеки">
          <Table
            size="small"
            loading={productLoading}
            pagination={false}
            columns={offerColumns}
            dataSource={product?.offers.map((item) => ({
              key: item.store.id,
              name: item.store.name,
              quantity: item.quantity,
              price: (item.price * 1).toLocaleString("ru"),
            }))}
          />
        </Card>
      </Col>
    </Row>
  );
};

export default View;
