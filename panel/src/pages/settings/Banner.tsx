import { FC, useState } from "react";
import {
  Button,
  Card,
  Col,
  Row,
  Space,
  Modal,
  Form,
  Input,
  Upload,
  Checkbox
} from "antd";
import type { UploadFile } from "antd/es/upload/interface";
import { PlusOutlined, InboxOutlined, DeleteOutlined } from "@ant-design/icons";
import { DragDropContext, Droppable, Draggable } from "react-beautiful-dnd";
import punycode from 'punycode/';

import { Banner as BaseBanner } from "../../components/banner";
import {
  useAddBannerMutation,
  useDeleteBannerMutation,
  useFetchBannersQuery,
  useUpdateSortBannersMutation,
} from "../../services/BannerService";
import { IBanner } from "../../models/IBanner";

const reorder = (list: IBanner[], startIndex: number, endIndex: number) => {
  const result = Array.from(list);
  const [removed] = result.splice(startIndex, 1);
  result.splice(endIndex, 0, removed);

  return result.map((item, index) => ({ id: item.id, sort: index }));
};

const grid = 5;
const getListStyle = (isDraggingOver: boolean) => ({
  display: "flex",
  padding: grid,
});
const getItemStyle = (isDragging: boolean, draggableStyle: any) => ({
  userSelect: "none",
  margin: `0 ${grid}px 0 0`,
  ...draggableStyle,
});

const DraggableBanner: FC<{
  index: number;
  banner: IBanner;
  onDelete: (id: number) => void;
}> = ({ index, banner, onDelete }) => {
  return (
    <>
      <Draggable draggableId={banner.id.toString()} index={index}>
        {(provided: any, snapshot: any) => (
          <div
            ref={provided.innerRef}
            {...provided.draggableProps}
            {...provided.dragHandleProps}
            style={getItemStyle(
              snapshot.isDragging,
              provided.draggableProps.style
            )}
          >
            <BaseBanner key={banner.id} banner={banner} onDelete={onDelete} />
          </div>
        )}
      </Draggable>
      {banner.type === 2 ? <small title="Ссылка для мобилки">{banner.picture.main.replace('120на80.рф', punycode.toASCII('120на80.рф'))}</small> : null}
    </>
  );
};

interface FormDataType {
  title: string;
  type?: boolean;
  description?: string;
  files: UploadFile[];
}

const Banner: FC = () => {
  const [fileList, setFileList] = useState<UploadFile[]>([]);
  const [openModal, setOpenModal] = useState<boolean>(false);
  const { data, isFetching } = useFetchBannersQuery();
  const [addBanner] = useAddBannerMutation();
  const [
    updateSortBanners,
    { isLoading: isUpdating },
  ] = useUpdateSortBannersMutation();
  const [deleteBanner] = useDeleteBannerMutation();
  const [form] = Form.useForm<FormDataType>();

  const handleBeforeUpload = (file: UploadFile) => {
    if (fileList.length < 2) {
      file.uid =
        fileList.findIndex((item) => item.uid === "main") < 0
          ? "main"
          : "mobile";

      setFileList((old) => [...old, file]);
    }

    return false;
  };

  const handleRemove = (file: UploadFile) => {
    setFileList((oldList) => oldList.filter((item) => item.uid !== file.uid));
  };

  const customRender = (_: any, file: UploadFile, __: any, { remove }: any) => (
    <div className="ant-upload-list-item ant-upload-list-item-undefined ant-upload-list-item-list-type-picture">
      <div className="ant-upload-list-item-info">
        <span className="ant-upload-span">
          <a
            className="ant-upload-list-item-thumbnail"
            href={file.thumbUrl}
            target="_blank"
            rel="noopener noreferrer"
          >
            <img
              alt={file.name}
              className="ant-upload-list-item-image"
              src={file.thumbUrl}
            />
          </a>
          <span className="ant-upload-list-item-name" title={file.name}>
            {file.name}
            <span style={{ color: "#52c41a" }}>
              {file.uid === "main"
                ? " (Обычная версия)"
                : " (Мобильная версия)"}
            </span>
          </span>
          <span className="ant-upload-list-item-card-actions picture">
            <Button
              type="text"
              icon={<DeleteOutlined style={{ color: "#ff4d4f" }} />}
              onClick={remove}
            />
          </span>
        </span>
      </div>
    </div>
  );

  const onDragEnd = (result: any) => {
    if (!result.destination || !data) return;

    updateSortBanners(
      reorder(data, result.source.index, result.destination.index)
    );
  };

  const handleAddBanner = async () => {
    const values = await form.validateFields();
    const data = new FormData();

    data.append("title", values.title);
    values.type && data.append("type", '2');
    values.description && data.append("description", values.description);
    values.files.forEach((item) => {
      item.originFileObj &&
        data.append(`files[${item.uid}]`, item.originFileObj);
    });

    addBanner(data);
    setOpenModal(false);
  };

  const handleCancel = () => {
    form.resetFields();
    setFileList([]);
    setOpenModal(false);
  };

  return (
    <Row gutter={[16, 16]}>
      <Col span={24}>
        <h2>Баннер</h2>
      </Col>
      <Col span={24}>
        <Card
          loading={isFetching || isUpdating}
          title={
            <div style={{ display: "flex", justifyContent: "space-between" }}>
              <span>Всего {data?.length || 0} записи</span>
              <Button
                type="primary"
                disabled={isFetching}
                onClick={() => setOpenModal(true)}
              >
                <PlusOutlined />
              </Button>
            </div>
          }
        >
          <DragDropContext onDragEnd={onDragEnd}>
            <Droppable droppableId="droppable">
              {(provided: any, snapshot: any) => (
                <div
                  ref={provided.innerRef}
                  style={getListStyle(snapshot.isDraggingOver)}
                  {...provided.droppableProps}
                >
                  <Space align="center" direction="vertical" size={32}>
                    {data?.map((banner, index) => (
                      <DraggableBanner
                        key={banner.id}
                        index={index}
                        banner={banner}
                        onDelete={deleteBanner}
                      />
                    ))}
                  </Space>
                  {provided.placeholder}
                </div>
              )}
            </Droppable>
          </DragDropContext>
        </Card>
      </Col>

      <Modal
        title="Добавить новый баннер"
        centered
        open={openModal}
        okText="Добавить"
        cancelText="Отменить"
        onOk={handleAddBanner}
        onCancel={handleCancel}
      >
        <Form form={form} layout="vertical">
          <Form.Item name="title" required>
            <Input placeholder="Введите название баннера" />
          </Form.Item>
          <Form.Item name="description">
            <Input.TextArea
              rows={3}
              placeholder="Описание для баннера (не обязательно)"
            />
          </Form.Item>
          <Form.Item name="type" valuePropName="checked">
            <Checkbox>Для мобильного</Checkbox>
          </Form.Item>
          <Form.Item
            name="files"
            valuePropName="fileList"
            getValueFromEvent={(e) => (Array.isArray(e) ? e : e?.fileList)}
          >
            <Upload.Dragger
              accept=".webp, .jpg, .jpeg"
              listType="picture"
              beforeUpload={handleBeforeUpload}
              onRemove={handleRemove}
              itemRender={customRender}
              disabled={fileList.length > 1}
            >
              <p className="ant-upload-drag-icon">
                <InboxOutlined />
              </p>
              <p className="ant-upload-text">Нажмите или перетащите файл</p>
              <p className="ant-upload-hint">
                Поддерживаемые форматы файла (webp, jpeg)
              </p>
            </Upload.Dragger>
          </Form.Item>
        </Form>
      </Modal>
    </Row>
  );
};

export default Banner;
