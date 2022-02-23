import React from "react";
import { Card, Space } from 'antd';
import { EditOutlined, CheckOutlined, CloseOutlined } from '@ant-design/icons';
import { IProduct } from "../../models/IProduct";
import { ICategory } from "../../models/ICategory";
import { productApi } from "../../services/ProductService";
import { EditorState, convertFromHTML, ContentState } from 'draft-js';
import { convertToHTML } from 'draft-convert';
import { Editor } from 'react-draft-wysiwyg';
import 'react-draft-wysiwyg/dist/react-draft-wysiwyg.css';

interface PropsType {
    product?: IProduct;
    loading: boolean;
}

const ViewDescription: React.FC<PropsType> = ({ product, loading }) => {
    const [edit, setEdit] = React.useState<boolean>(false);
    const [editorState, setEditorState] = React.useState(() => {
        const blocksFromHTML = convertFromHTML(product?.description || '');
        const contentState = ContentState.createFromBlockArray(blocksFromHTML.contentBlocks, blocksFromHTML.entityMap);

        return EditorState.createWithContent(contentState);
    });
    const [updateProduct, {isLoading: updateLoading}] = productApi.useUpdateDescriptionProductMutation();

    const options = ['inline', 'blockType', 'list', 'link', 'embedded', 'emoji'];

    const getCategoryTree: any = (categories: ICategory[]) => categories?.map(item => ({
        title: item.name,
        value: item.id,
        children: getCategoryTree(item.children)
    }));

    const handleSave = () => {
        const data = {description: convertToHTML(editorState.getCurrentContent())};
        if (product) updateProduct({ slug: product.slug, data });
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
            {edit ? <Editor toolbar={{options}} editorState={editorState} onEditorStateChange={setEditorState} localization={{locale: 'ru'}} /> :
                <div dangerouslySetInnerHTML={{__html: product?.description || ''}}
            />}
        </Card>
    );
}

export { ViewDescription };
