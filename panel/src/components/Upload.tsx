import React from 'react';
import { Upload as BaseUpload, UploadProps } from 'antd';
import { PlusOutlined } from '@ant-design/icons';
import axios from 'axios';
import { API_URL } from '../services/api';

interface PropsType {
  slug: string
}

const Upload: React.FC<PropsType> = ({ slug }) => {
  const [fileList, setFileList] = React.useState<any>([]);

  const onChange = (info: UploadProps) => {
    setFileList(info.fileList);
  };

  const uploadImage = async (options: any) => {
    const { onSuccess, onError, file, onProgress } = options;

    const fmData = new FormData();
    const config = {
      headers: { "content-type": "multipart/form-data" },
      onUploadProgress: (event: any) => onProgress({ percent: (event.loaded / event.total) * 100 })
    };

    fmData.append("file", file);
    try {
      const res = await axios.post(
        API_URL + '/product/upload/' + slug,
        fmData,
        config
      );

      onSuccess("Ok");
      console.log("server res: ", res);
    } catch (err) {
      console.log("Eroor: ", err);
      onError({ err });
    }
  };

  const onPreview = async (file: any) => {
    let src = file.url;
    if (!src) {
      src = await new Promise(resolve => {
        const reader = new FileReader();
        reader.readAsDataURL(file.originFileObj);
        reader.onload = () => resolve(reader.result);
      });
    }
    const image = new Image();
    image.src = src;
    const imgWindow = window.open(src);
    imgWindow?.document.write(image.outerHTML);
  };

  return (
    <BaseUpload
      accept="image/*"
      customRequest={uploadImage}
      listType="picture-card"
      fileList={fileList}
      onChange={onChange}
      onPreview={onPreview}
    >
      <div>
        <PlusOutlined />
        <div style={{ marginTop: 8 }}>Upload</div>
      </div>
    </BaseUpload>
  );
};

export { Upload }
