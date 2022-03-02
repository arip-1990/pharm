import React from 'react';
import { Upload as BaseUpload, Image as BaseImage, Space, Popconfirm } from 'antd';
import { PlusOutlined, CloseCircleOutlined } from '@ant-design/icons';
import { useAddPhotoProductMutation, useDeletePhotoProductMutation } from '../services/ProductService';

interface PropsType {
  slug: string;
  photos: {id: number, url: string}[];
}

const Upload: React.FC<PropsType> = ({ slug, photos }) => {
  const [addPhoto] = useAddPhotoProductMutation();
  const [deletePhoto] = useDeletePhotoProductMutation();

  const uploadImage = async (options: any) => {
    const { onSuccess, onError, file, onProgress } = options;

    const data = new FormData();
    data.append("file", file);
    try {
      await addPhoto({slug, data, onProgress: (event: any) => onProgress({ percent: (event.loaded / event.total) * 100 })}).unwrap();
      onSuccess("Ok");
    }
    catch (error) {
      console.log("Error: ", error);
      onError({ error });
    }
  };

  return (
    <Space>
      {photos.length < 10 ? <BaseUpload
        accept="image/*"
        customRequest={uploadImage}
        listType="picture-card"
        fileList={[]}
      >
        <div>
          <PlusOutlined />
          <div style={{ marginTop: 8 }}>Загрузить</div>
        </div>
      </BaseUpload> : null}
      <BaseImage.PreviewGroup>
        <Space>
          {photos
            .filter((item) => !!item.id)
            .map((item) => (
              <div key={item.id} className='media'>
                <Popconfirm
                  title="Вы уверены, что хотите удалить?"
                  onConfirm={() => deletePhoto(item.id)}
                  okText="Да"
                  cancelText="Нет"
                >
                  <CloseCircleOutlined />
                </Popconfirm>
                <BaseImage width={140} src={item.url} />
              </div>
          ))}
        </Space>
      </BaseImage.PreviewGroup>
    </Space>
  );
};

export { Upload }
