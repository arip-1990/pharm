import React from "react";
import { Card, Space, Button } from "antd";
import { useDeletePhotosProductMutation, useUpdatePhotosProductMutation } from "../../services/ProductService";
import { Upload } from "../Upload";

interface PropsType {
    slug: string;
    photos: {id: number, sort: number, url: string}[];
    loading: boolean;
}

const ViewPhotos: React.FC<PropsType> = ({ slug, photos, loading }) => {
    const [changePhotos, setChangePhotos] = React.useState<boolean>(false);
    const [deletePhotoIds, setDeletePhotoIds] = React.useState<number[]>([]);
    const [items, setItems] = React.useState<{id: number, sort: number, url: string}[]>([]);
    const [updatePhotos, {isLoading: updateLoading}] = useUpdatePhotosProductMutation();
    const [deletePhotos, {isLoading: deleteLoading}] = useDeletePhotosProductMutation();

    React.useEffect(() => {
        setItems(photos);
        setChangePhotos(false);
        setDeletePhotoIds([]);
    }, [photos]);

    const selectDeletePhotos = (id: number, add: boolean) => {
        if (add) setDeletePhotoIds([...deletePhotoIds, id])
        else setDeletePhotoIds(deletePhotoIds.filter(item => item !== id));
    }

    const handleChangePhotos = (items: {id: number, sort: number, url: string}[]) => {
        setChangePhotos(true);
        setItems(items);
    }

    return (
        <Card
          title="Фотографии"
          loading={loading}
          extra={
            <Space>
              <Button
                type="primary"
                disabled={!changePhotos}
                loading={updateLoading}
                onClick={() => updatePhotos({slug, items})}
            >Сохранить</Button>
              <Button
                type="primary" danger
                disabled={!deletePhotoIds.length}
                loading={deleteLoading}
                onClick={() => deletePhotos({slug, items: deletePhotoIds})}
              >Удалить</Button>
            </Space>
          }
        >
          <Upload slug={slug || ''} photos={items} changePhotos={handleChangePhotos} deletePhoto={selectDeletePhotos} />
        </Card>
    );
}

export { ViewPhotos };
