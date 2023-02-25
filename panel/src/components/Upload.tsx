import React from "react";
import {
  Upload as BaseUpload,
  Image as BaseImage,
  Space,
  Popconfirm,
  notification,
  Typography,
} from "antd";
import { PlusOutlined, CloseCircleOutlined } from "@ant-design/icons";
import { DragDropContext, Droppable, Draggable } from "react-beautiful-dnd";
import {
  useAddPhotoProductMutation,
  useDeletePhotosProductMutation,
} from "../services/ProductService";
import classNames from "classnames";
import { IPhoto } from "../models/IPhoto";

const reorder = (
  list: { id: number; sort: number; url: string }[],
  startIndex: number,
  endIndex: number
) => {
  const result = Array.from(list);
  const [removed] = result.splice(startIndex, 1);
  result.splice(endIndex, 0, removed);

  return result.map((item, index) => ({ ...item, sort: index }));
};

const grid = 5;

const getItemStyle = (isDragging: boolean, draggableStyle: any) => ({
  userSelect: "none",
  margin: `0 ${grid}px 0 0`,
  ...draggableStyle,
});

const getListStyle = (isDraggingOver: boolean) => ({
  display: "flex",
  padding: grid,
});

interface ImagePropsType {
  item: IPhoto;
  selectPhoto: (id: number, add: boolean) => void;
  deletePhoto: (id: number) => void;
}

const Image: React.FC<ImagePropsType> = ({
  item,
  selectPhoto,
  deletePhoto,
}) => {
  const [active, setActive] = React.useState<boolean>(false);

  const handleClick = () => {
    setActive(!active);
    selectPhoto(item.id, !active);
  };

  return (
    <div className="media">
      <Popconfirm
        title="Вы уверены, что хотите удалить?"
        onConfirm={() => deletePhoto(item.id)}
        okText="Да"
        cancelText="Нет"
      >
        <CloseCircleOutlined />
      </Popconfirm>
      <span
        className={classNames("anticon-select", { active })}
        onClick={handleClick}
      />
      <BaseImage width={140} src={item.url} />
      {item.status ? (
        <Typography.Text
          style={{ fontSize: "0.75rem", textAlign: "center" }}
          type="success"
        >
          (Проверен)
        </Typography.Text>
      ) : (
        <Typography.Text
          style={{ fontSize: "0.75rem", textAlign: "center" }}
          type="danger"
        >
          (Не проверен)
        </Typography.Text>
      )}
    </div>
  );
};

interface UploadPropsType {
  slug: string;
  photos: IPhoto[];
  changePhotos: (items: IPhoto[]) => void;
  selectPhoto: (id: number, add: boolean) => void;
}

const Upload: React.FC<UploadPropsType> = ({
  slug,
  photos,
  changePhotos,
  selectPhoto,
}) => {
  const [addPhoto] = useAddPhotoProductMutation();
  const [deletePhotos] = useDeletePhotosProductMutation();

  const uploadImage = async (options: any) => {
    const { onSuccess, onError, file, onProgress } = options;

    const data = new FormData();
    data.append("file", file);

    try {
      await addPhoto({
        slug,
        data,
        onProgress: (event: ProgressEvent) => {
          onProgress({ percent: (event.loaded / event.total) * 100 });
        },
      }).unwrap();

      onSuccess("Ok");
    } catch (error) {
      const err = error as any;
      notification.error(err.data);
      console.log("Error: ", err);
      onError({ error });
    }
  };

  const onDragEnd = (result: any) => {
    if (!result.destination) return;

    const newItems: any = reorder(
      photos,
      result.source.index,
      result.destination.index
    );

    changePhotos(newItems);
  };

  return (
    <Space>
      {photos.length < 5 ? (
        <BaseUpload
          accept="image/*"
          customRequest={uploadImage}
          listType="picture-card"
          fileList={[]}
        >
          <div>
            <PlusOutlined />
            <div style={{ marginTop: 8 }}>Загрузить</div>
          </div>
        </BaseUpload>
      ) : null}

      <DragDropContext onDragEnd={onDragEnd}>
        <Droppable droppableId="droppable" direction="horizontal">
          {(provided: any, snapshot: any) => (
            <div
              ref={provided.innerRef}
              style={getListStyle(snapshot.isDraggingOver)}
              {...provided.droppableProps}
            >
              <BaseImage.PreviewGroup>
                <Space>
                  {photos
                    .filter((item) => !!item.id)
                    .map((item, index) => (
                      <Draggable
                        key={item.id}
                        draggableId={item.id.toString()}
                        index={index}
                      >
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
                            <Image
                              item={item}
                              selectPhoto={selectPhoto}
                              deletePhoto={(id) => deletePhotos([id])}
                            />
                          </div>
                        )}
                      </Draggable>
                    ))}
                </Space>
              </BaseImage.PreviewGroup>
              {provided.placeholder}
            </div>
          )}
        </Droppable>
      </DragDropContext>
    </Space>
  );
};

export { Upload };
