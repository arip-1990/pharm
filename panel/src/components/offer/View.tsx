import React from "react";
import { Link, useParams } from "react-router-dom";
import { Card, Col, Row, Table } from "antd";
import { useFetchOfferQuery } from "../../services/OfferService";

const baseColumns = [{ dataIndex: "key" }, { dataIndex: "value" }];

const itemColumns = [
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
  const { data: offer, isFetching } = useFetchOfferQuery(slug || "", {
    skip: !slug,
  });

  return (
    <Row gutter={[32, 32]}>
      <Col span={24}>
        <h2 style={{ margin: 0 }}>Товар {offer?.code}</h2>
      </Col>

      <Col span={24}>
        <Card title="Товар">
          <Table
            size="small"
            loading={isFetching}
            showHeader={false}
            pagination={false}
            columns={baseColumns}
            dataSource={
              offer && [
                { key: "Код", value: offer.code },
                {
                  key: "Наименование",
                  value: (
                    <Link to={`/product/${offer.slug}`}>{offer.name}</Link>
                  ),
                },
              ]
            }
          />
        </Card>
      </Col>

      <Col span={24}>
        <Card title="Аптеки">
          <Table
            size="small"
            loading={isFetching}
            pagination={false}
            columns={itemColumns}
            dataSource={offer?.items.map((item) => ({
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
