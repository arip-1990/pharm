import React from "react";
import { Card, Space, Button, Dropdown, Menu, Typography } from "antd";
import {
  useDeletePhotosProductMutation,
  useUpdatePhotosProductMutation,
  useUpdateStatusPhotosProductMutation,
} from "../../services/ProductService";
import { Upload } from "../Upload";
import { IPhoto } from "../../models/IPhoto";

interface PropsType {
  slug: string;
  photos: IPhoto[];
  loading: boolean;
}

const ViewPhotos: React.FC<PropsType> = ({ slug, photos, loading }) => {
  const [changePhotos, setChangePhotos] = React.useState<boolean>(false);
  const [actionPhotoIds, setActionPhotoIds] = React.useState<number[]>([]);
  const [items, setItems] = React.useState<IPhoto[]>([]);
  const [
    updatePhotos,
    { isLoading: updateLoading },
  ] = useUpdatePhotosProductMutation();
  const [
    updateStatusPhotos,
    { isLoading: updateStatusLoading },
  ] = useUpdateStatusPhotosProductMutation();
  const [
    deletePhotos,
    { isLoading: deleteLoading },
  ] = useDeletePhotosProductMutation();

  React.useEffect(() => {
    setItems(photos);
    setChangePhotos(false);
    setActionPhotoIds([]);
  }, [photos]);

  const selectDeletePhotos = (id: number, add: boolean) => {
    if (add) setActionPhotoIds([...actionPhotoIds, id]);
    else setActionPhotoIds(actionPhotoIds.filter((item) => item !== id));
  };

  const handleChangePhotos = (items: IPhoto[]) => {
    setChangePhotos(true);
    setItems(items);
  };

  const handleSelect = ({ key }: any) => {
    console.log(key);
    if (key === "delete") {
      deletePhotos(actionPhotoIds);
    } else if (key === "checked") {
      updateStatusPhotos(actionPhotoIds);
    }
  };

  return (
    <Card
      title="Фотографии"
      loading={loading}
      extra={
        <Space>
          <Dropdown
            disabled={!actionPhotoIds.length}
            placement="bottomLeft"
            overlay={
              <Menu onClick={handleSelect}>
                <Menu.Item key="checked">
                  <Typography.Text type="success">Проверен</Typography.Text>
                </Menu.Item>
                <Menu.Item key="delete">
                  <Typography.Text type="danger">Удалить</Typography.Text>
                </Menu.Item>
              </Menu>
            }
            arrow
          >
            <Button loading={deleteLoading || updateStatusLoading}>
              Действие
            </Button>
          </Dropdown>
          <Button
            type="primary"
            disabled={!changePhotos}
            loading={updateLoading}
            onClick={() => updatePhotos(items)}
          >
            Сохранить
          </Button>
        </Space>
      }
    >
      <Upload
        slug={slug || ""}
        photos={items}
        changePhotos={handleChangePhotos}
        deletePhoto={selectDeletePhotos}
      />
    </Card>
  );
};

export { ViewPhotos };
