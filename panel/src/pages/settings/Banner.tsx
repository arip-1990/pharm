import { FC, useEffect, useState } from "react";
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
  Select,
  Breadcrumb,
  Dropdown,
} from "antd";
import type { MenuProps } from "antd";
import type { UploadFile } from "antd/es/upload/interface";
import {
  PlusOutlined,
  InboxOutlined,
  DeleteOutlined,
  FolderOutlined,
} from "@ant-design/icons";
import { DragDropContext, Droppable, Draggable } from "react-beautiful-dnd";
import punycode from "punycode/";

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
      {banner.type === "mobile" ? (
        <small title="Ссылка для мобилки">
          {banner.picture.main?.replace(
            "120на80.рф",
            punycode.toASCII("120на80.рф")
          )}
        </small>
      ) : null}
    </>
  );
};

interface FormDataType {
  title: string;
  type?: "main" | "extra" | "all" | "mobile";
  description?: string;
  files: UploadFile[];
  folder?: string;
  link?: string;
}

const Banner: FC = () => {
  const [currentPath, setCurrentPath] = useState<string>("/");
  const [newPath, setNewPath] = useState<string>();
  const [fileList, setFileList] = useState<UploadFile[]>([]);
  const [openModal, setOpenModal] = useState<boolean>(false);
  const [modalType, setModalType] = useState<"folder" | "banner">("folder");
  const { data, isFetching } = useFetchBannersQuery();

  const [addBanner] = useAddBannerMutation();
  const [
    updateSortBanners,
    { isLoading: isUpdating },
  ] = useUpdateSortBannersMutation();
  const [deleteBanner] = useDeleteBannerMutation();
  const [form] = Form.useForm<FormDataType>();

  const extraItems: MenuProps["items"] = [
    {
      label: (
        <a
          href="#"
          onClick={() => {
            setModalType("folder");
            setOpenModal(true);
          }}
        >
          Создать папку
        </a>
      ),
      key: "addFolder",
    },
  ];

  useEffect(() => {
    if (currentPath !== "/") {
      extraItems.push({
        label: (
          <a
            href="#"
            onClick={() => {
              setModalType("banner");
              setOpenModal(true);
            }}
          >
            Добавить баннер
          </a>
        ),
        key: "addBanner",
      });
    }
  }, [currentPath, data]);

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

  const handleForm = async () => {
    if (modalType === "banner") await handleAddBanner();
    else {
      const values = await form.validateFields();
      setNewPath(currentPath + values.folder);
    }

    setOpenModal(false);
    form.resetFields();
  };

  const handleAddBanner = async () => {
    const values = await form.validateFields();
    const data = new FormData();

    data.append("title", values.title);
    data.append("path", currentPath);
    values.link && data.append("link", values.link);
    values.type && data.append("type", values.type);
    values.description && data.append("description", values.description);
    values.files.forEach((item) => {
      item.originFileObj &&
        data.append(`files[${item.uid}]`, item.originFileObj);
    });

    addBanner(data);
  };

  const handleCancel = () => {
    form.resetFields();
    setFileList([]);
    setOpenModal(false);
  };

  // Navigation
  const handleNavigation = (path: string, index?: number) => {
    if (index) {
      const newCurrentPath = currentPath.split("/").slice(0, index).join("/");
      newCurrentPath.length > 0
        ? setCurrentPath(newCurrentPath)
        : setCurrentPath("/");
    } else
      setCurrentPath(
        (oldCurrent) => oldCurrent.replace(/\/$/, "") + `/${path}`
      );
  };

  // удаление дубликатов
  const uniquePaths = (paths: string[]): string[] => {
    return Array.from(
      new Set(
        paths.filter(
          (path) => path.split("/").filter((path) => path).length === 1
        )
      )
    );
  };

  const getFolder = () => {
    let paths =
      data
        ?.filter((item) => item.path.startsWith(currentPath))
        .map((item) => item.path.replace(`${currentPath}`, "")) || [];
    if (newPath) paths.push(newPath.replace(`${currentPath}`, ""));

    return uniquePaths(paths).map((path) => (
      <div key={path} style={{ margin: "0px 10px 0px 10px" }}>
        <FolderOutlined
          style={{
            fontSize: "5rem",
            color: "rgba(0,0,0,0.65)",
            cursor: "pointer",
          }}
          onDoubleClick={() => handleNavigation(path)}
        />
        <div
          style={{
            display: "flex",
            justifyContent: "center",
            alignItems: "center",
          }}
        >
          <p>{path.replace("/", "")}</p>
        </div>
      </div>
    ));
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
            <Breadcrumb
              items={`root${currentPath}`
                .split("/")
                .filter((item) => item)
                .map((item, index, paths) => ({
                  title:
                    paths.length - 1 === index ? (
                      item
                    ) : (
                      <a
                        href="#"
                        onClick={() => handleNavigation(item, index + 1)}
                      >
                        {item}
                      </a>
                    ),
                }))}
            />
          }
          extra={
            <Dropdown
              disabled={isFetching}
              menu={{ items: extraItems }}
              trigger={["click"]}
            >
              <Button type="primary">
                <PlusOutlined />
              </Button>
            </Dropdown>
          }
        >
          <div style={{ display: "flex" }}>{getFolder()}</div>

          <DragDropContext onDragEnd={onDragEnd}>
            <Droppable droppableId="droppable">
              {(provided: any, snapshot: any) => (
                <div
                  ref={provided.innerRef}
                  style={getListStyle(snapshot.isDraggingOver)}
                  {...provided.droppableProps}
                >
                  <Space align="center" direction="vertical" size={32}>
                    {data
                      ?.filter((banner) => currentPath === banner.path)
                      .map((banner, index) =>
                        banner.type === "mobile" ? (
                          <DraggableBanner
                            key={banner.id}
                            index={index}
                            banner={banner}
                            onDelete={deleteBanner}
                          />
                        ) : (
                          <DraggableBanner
                            key={banner.id}
                            index={index}
                            banner={banner}
                            onDelete={deleteBanner}
                          />
                        )
                      )}
                  </Space>
                  {provided.placeholder}
                </div>
              )}
            </Droppable>
          </DragDropContext>
        </Card>
      </Col>

      <Modal
        title={
          modalType === "banner" ? "Добавить новый баннер" : "Создать папку"
        }
        centered
        open={openModal}
        okText={modalType === "banner" ? "Добавить" : "Создать"}
        cancelText="Отменить"
        onOk={handleForm}
        onCancel={handleCancel}
      >
        <Form form={form} layout="vertical">
          {modalType === "banner" ? (
            <>
              <Form.Item name="title" required>
                <Input placeholder="Введите название баннера" />
              </Form.Item>
              <Form.Item name="description">
                <Input.TextArea
                  rows={3}
                  placeholder="Описание для баннера (не обязательно)"
                />
              </Form.Item>
              {currentPath.includes("mobile") ? (
                <Form.Item name="type" initialValue="mobile" hidden>
                  <Input type="hidden" />
                </Form.Item>
              ) : (
                <Form.Item name="type">
                  <Select placeholder="Выберите тип баннера">
                    <Select.Option value="main">Основной баннер</Select.Option>
                    <Select.Option value="extra">
                      Дополнительный баннер
                    </Select.Option>
                  </Select>
                </Form.Item>
              )}
              <Form.Item name="link">
                <Input placeholder="Укажите ссылку для баннера (не обязательно)" />
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
            </>
          ) : (
            <Form.Item name="folder" required>
              <Input placeholder="Введите название папки" />
            </Form.Item>
          )}
        </Form>
      </Modal>
    </Row>
  );
};

export default Banner;
