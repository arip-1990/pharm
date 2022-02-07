import React from "react";
import { Card, Space } from 'antd';
import { EditOutlined, CheckOutlined, CloseOutlined } from '@ant-design/icons';
import { IProduct } from "../../models/IProduct";
import { ICategory } from "../../models/ICategory";
import { productApi } from "../../services/ProductService";
import { EditorState } from 'draft-js';
import { convertToHTML } from 'draft-convert';
import { Editor } from 'react-draft-wysiwyg';
import 'react-draft-wysiwyg/dist/react-draft-wysiwyg.css';

interface PropsType {
    product?: IProduct;
    loading: boolean;
}

const ViewDescription: React.FC<PropsType> = ({ product, loading }) => {
    const [edit, setEdit] = React.useState<boolean>(false);
    const [editorState, setEditorState] = React.useState(() => EditorState.createEmpty());
    const [updateProduct, {isLoading: updateLoading}] = productApi.useUpdateDescriptionProductMutation();

    const options = ['inline', 'blockType', 'fontSize', 'fontFamily', 'list', 'textAlign', 'link', 'embedded', 'emoji', 'remove', 'history'];

    const getCategoryTree: any = (categories: ICategory[]) => categories?.map(item => ({
        title: item.name,
        value: item.id,
        children: getCategoryTree(item.children)
    }));

    const handleSave = () => {
        const data = convertToHTML(editorState.getCurrentContent());
        console.log(data);
        if (product) updateProduct({ slug: product.slug, data });
        setEdit(false);
    }

    const handleReset = () => {
        setEdit(false);
    }

    console.log(product?.description);

    return (
        <Card
            title="Описание"
            loading={loading}
            extra={edit
                ? <Space><CheckOutlined style={{color: '#52c41a'}} onClick={handleSave} /><CloseOutlined style={{color: '#ff4d4f'}} onClick={handleReset} /></Space>
                : <EditOutlined style={{color: '#1890ff'}} onClick={() => setEdit(true)} />
            }
        >
            {edit ? <Editor toolbar={{options}} editorState={editorState} onEditorStateChange={setEditorState} localization={{locale: 'ru'}} /> :
                <div dangerouslySetInnerHTML={{__html: product?.description || ''}}
            />}
        </Card>
    );
}

export { ViewDescription };
