import React from 'react';
import {Card, Col, Row} from "antd";

const NotActivePhoto = () => {
  return (
    <Row gutter={[16, 16]}>
      <Col span={24}>
        <h2>Конкурс рисунков</h2>
      </Col>
      <Col span={24}>
        <Card
          title={
            <h3>Неактивные фотографии</h3>
          }
        >
          <h1>Hello world</h1>
        </Card>
      </Col>
    </Row>
  );
};

export default NotActivePhoto;
