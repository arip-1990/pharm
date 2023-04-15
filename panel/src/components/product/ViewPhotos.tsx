import React from "react";
import { Card, Space, Button } from "antd";
import {
  useDeletePhotosProductMutation,
  useUpdatePhotosProductMutation,
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
    deletePhotos,
    { isLoading: deleteLoading },
  ] = useDeletePhotosProductMutation();

  React.useEffect(() => {
    setItems(photos);
    setChangePhotos(false);
    setActionPhotoIds([]);
  }, [photos]);

  const selectPhoto = (id: number, add: boolean) => {
    if (add) setActionPhotoIds([...actionPhotoIds, id]);
    else setActionPhotoIds(actionPhotoIds.filter((item) => item !== id));
  };

  const handleChangePhotos = (items: IPhoto[]) => {
    setChangePhotos(true);
    setItems(items);
  };

  const handleDelete = () => deletePhotos(actionPhotoIds);

  return (
    <Card
      title="Фотографии"
      loading={loading}
      extra={
        <Space>
          <Button
            type="primary"
            disabled={!actionPhotoIds.length}
            loading={deleteLoading}
            danger
            onClick={handleDelete}
          >
            Удалить
          </Button>
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
        selectPhoto={selectPhoto}
      />
    </Card>
  );
};

export { ViewPhotos };
