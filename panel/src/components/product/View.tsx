import React from 'react';
import {Card, TablePaginationConfig, Input, Space, Button, Image} from 'antd';
import {SearchOutlined} from '@ant-design/icons';
import { productApi } from '../../services/ProductService';
import { Table } from '..';
import { useParams } from 'react-router-dom';

const View: React.FC = () => {
  const {slug} = useParams();
  const {data: product, isLoading: fetchLoading} = productApi.useFetchProductQuery(slug || '', {skip: !slug});

  return (
    <Card title={product?.name}>
    </Card>
  )
}

export default View;
