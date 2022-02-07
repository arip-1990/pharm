import React from "react";
import { Card, Input, Switch, TreeSelect, Form, Space } from 'antd';
import { EditOutlined, CheckOutlined, CloseOutlined } from '@ant-design/icons';
import { Table } from "..";
import { IProduct } from "../../models/IProduct";
import { categoryApi } from "../../services/CategoryService";
import { ICategory } from "../../models/ICategory";
import { productApi } from "../../services/ProductService";

interface PropsType {
    product?: IProduct;
    loading: boolean;
}

const ViewBase: React.FC<PropsType> = ({product, loading}) => {
    const [edit, setEdit] = React.useState<boolean>(false);
    const {data: categories} = categoryApi.useFetchCategoriesQuery();
    const [updateProduct, {isLoading: updateLoading}] = productApi.useUpdateProductMutation();
    const [form] = Form.useForm();

    const getCategoryTree: any = (categories: ICategory[]) => categories?.map(item => ({
        title: item.name,
        value: item.id,
        children: getCategoryTree(item.children)
    }));

    const generalColumns = [
        { dataIndex: "key", width: 136 },
        {
            dataIndex: "value",
            render: (item: any, record: {key: string, value: string | number}) => {
                if (edit) {
                    switch (record.key) {
                        case 'Штрих-код':
                            return <Form.Item style={{margin: 0}} name='barcode' initialValue={item}><Input /></Form.Item>;
                        case 'Название':
                            return <Form.Item style={{margin: 0}} name='name' initialValue={item}><Input /></Form.Item>;
                        case 'Статус':
                            return <Form.Item style={{margin: 0}} name='status' initialValue={item === 'Активен'} valuePropName="checked"><Switch /></Form.Item>;
                        case 'Категория':
                            return <Form.Item style={{margin: 0}} name='category' initialValue={item.id}><TreeSelect treeLine={{showLeafIcon: false}} treeData={getCategoryTree(categories)} /></Form.Item>;
                    }
                }
                return record.key === 'Категория' ? item?.name : item;
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
            title="Общий"
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
                    columns={generalColumns}
                    data={[
                        { key: "Код", value: product?.code },
                        { key: "Штрих-код", value: product?.barcode },
                        { key: "Название", value: product?.name },
                        { key: "Категория", value: product?.category },
                        {
                            key: "Статус",
                            value: product?.status ? "Активен" : "Не активен",
                        },
                    ]}
                />
            </Form>
        </Card>
    )
}

export { ViewBase };
