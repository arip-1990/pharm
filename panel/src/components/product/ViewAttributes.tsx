import React from "react";
import { Card, Input, Form, Space } from 'antd';
import { EditOutlined, CheckOutlined, CloseOutlined } from '@ant-design/icons';
import { Table, Editor } from "..";
import { IProduct } from "../../models/IProduct";
import { productApi } from "../../services/ProductService";

interface PropsType {
    product?: IProduct;
    loading: boolean;
}

const ViewAttributes: React.FC<PropsType> = ({product, loading}) => {
    const [edit, setEdit] = React.useState<boolean>(false);
    const [updateProduct] = productApi.useUpdateAttributesProductMutation();
    const [form] = Form.useForm();

    const columns = [
        { dataIndex: "key", width: 136 },
        { dataIndex: "type", colSpan: 0, render: () => undefined },
        {
            dataIndex: "value",
            render: (item: string, record: {key: string, type: string, value: string}) => {
                if (edit) {
                    switch (record.type) {
                        case 'string':
                            return <Form.Item style={{margin: 0}} name={record.key} initialValue={item}><Input /></Form.Item>;
                        // case 'number':
                        //     return <Form.Item style={{margin: 0}} name={record.key} initialValue={item}><InputNumber /></Form.Item>;
                        case 'text':
                            return <Form.Item style={{margin: 0}} name={record.key} initialValue={item}><Editor /></Form.Item>;
                    }
                }
                return record.type === 'text' ? <div dangerouslySetInnerHTML={{__html: item || ''}} /> : item;
            }
        }
    ];

    const handleSave = async () => {
        let data = await form.validateFields();
        if (product) updateProduct({slug: product.slug, data});
        setEdit(false);
    }

    const handleReset = () => {
        setEdit(false);
        form.resetFields();
    }

    return (
        <Card
            title="Аттрибуты"
            loading={loading}
            extra={edit
                ? <Space><CheckOutlined style={{color: '#52c41a'}} onClick={handleSave} /><CloseOutlined style={{color: '#ff4d4f'}} onClick={handleReset} /></Space>
                : <EditOutlined style={{color: '#1890ff'}} onClick={() => setEdit(true)} />
            }
        >
            <Form component={false} form={form}>
                <Table
                    size="small"
                    showHeader={false}
                    columns={columns}
                    data={product?.attributes.map(item => ({
                        key: item.attrubuteName,
                        type: item.attrubuteType,
                        value: item.value
                    }))}
                />
            </Form>
        </Card>
    )
}

export { ViewAttributes };
