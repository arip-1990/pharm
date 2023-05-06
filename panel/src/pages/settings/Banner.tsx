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
} from "antd";
import type { RcFile, UploadProps } from "antd/es/upload";
import type { UploadFile } from "antd/es/upload/interface";
import { PlusOutlined } from "@ant-design/icons";
import { DragDropContext, Droppable, Draggable } from "react-beautiful-dnd";

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

const getBase64 = (data: RcFile): Promise<string> =>
  new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.readAsDataURL(data);
    reader.onload = () => resolve(reader.result as string);
    reader.onerror = (error) => reject(error);
  });

const DraggableBanner: FC<{
  index: number;
  banner: IBanner;
  onDelete: (id: number) => void;
}> = ({ index, banner, onDelete }) => {
  return (
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
  );
};

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
  const [form] = Form.useForm();

  const normFile = (e: any) => {
    if (Array.isArray(e)) return e;
    return e?.fileList;
  };

  const handleChange: UploadProps["onChange"] = ({ fileList: newFileList }) => {
    console.log(newFileList);
    setFileList(newFileList);
  };

  const handleUploadFile = async (file: RcFile) => await getBase64(file);

  const onDragEnd = (result: any) => {
    if (!result.destination || !data) return;

    updateSortBanners(
      reorder(data, result.source.index, result.destination.index)
    );
  };

  const handleAddBanner = async () => {
    const values = await form.validateFields();
    form.resetFields();
    console.log(values);
    setOpenModal(false);
  };

  const handleCancel = () => {
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
        </Form>
        <Form.Item
          name="files"
          valuePropName="fileList"
          getValueFromEvent={normFile}
          required
        >
          <Upload
            accept=".avif, .webp, .jpg, .jpeg"
            multiple
            maxCount={2}
            listType="picture-card"
            fileList={fileList}
            action={handleUploadFile}
            onChange={handleChange}
          >
            <div>
              <PlusOutlined />
              <div style={{ marginTop: 8 }}>Загрузить</div>
            </div>
          </Upload>
        </Form.Item>
      </Modal>
    </Row>
  );
};

export default Banner;
