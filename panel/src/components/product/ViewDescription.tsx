import React from "react";
import { Card, Form, Space } from 'antd';
import { EditOutlined, CheckOutlined, CloseOutlined } from '@ant-design/icons';
import { IProduct } from "../../models/IProduct";
import { ICategory } from "../../models/ICategory";
import { useUpdateDescriptionProductMutation } from "../../services/ProductService";
import { Editor } from '..';
import 'react-draft-wysiwyg/dist/react-draft-wysiwyg.css';

interface PropsType {
    product?: IProduct;
    loading: boolean;
}

const ViewDescription: React.FC<PropsType> = ({ product, loading }) => {
    const [edit, setEdit] = React.useState<boolean>(false);
    const [updateProduct] = useUpdateDescriptionProductMutation();
    const [form] = Form.useForm();

    const getCategoryTree: any = (categories: ICategory[]) => categories?.map(item => ({
        title: item.name,
        value: item.id,
        children: getCategoryTree(item.children)
    }));

    const handleSave = async () => {
        const data = await form.validateFields();
        if (product) updateProduct({slug: product.slug, data});
        setEdit(false);
    }

    const handleReset = () => {
        setEdit(false);
    }

    return (
        <Card
            title="Описание"
            loading={loading}
            extra={edit
                ? <Space><CheckOutlined style={{color: '#52c41a'}} onClick={handleSave} /><CloseOutlined style={{color: '#ff4d4f'}} onClick={handleReset} /></Space>
                : <EditOutlined style={{color: '#1890ff'}} onClick={() => setEdit(true)} />
            }
        >
            <Form component={false} form={form}>
                {edit ? <Form.Item style={{margin: 0}} name='description' initialValue={product?.description}><Editor toolbar={true} /></Form.Item> :
                    <div dangerouslySetInnerHTML={{__html: product?.description || ''}}
                />}
            </Form>
        </Card>
    );
}

export { ViewDescription };
