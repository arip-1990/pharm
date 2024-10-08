import React from 'react';
import {Card, Col, Row} from "antd";
import KidsCard from "../../components/Kids/KidsCard";

const ActivePhoto = () => {

  return (
    <Row gutter={[16, 16]}>
      <Col span={24}>
        <h2>Конкурс рисунков</h2>
      </Col>
      <Col span={24}>
        <Card
          title={
            <h3>Активные фотографии</h3>
          }
        >
          <KidsCard published={true} />

        </Card>
      </Col>
    </Row>
  );
};

export default ActivePhoto;
