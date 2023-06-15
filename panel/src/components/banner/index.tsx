import { FC, useState } from "react";
import { Image, Popconfirm } from "antd";
import { DeleteTwoTone, FolderOutlined } from "@ant-design/icons";
import { IBanner } from "../../models/IBanner";
import banner from "../../pages/settings/Banner";

interface Props {
  banner: IBanner;
  onDelete: (id: number) => void;
}

const Banner: FC<Props> = ({ banner, onDelete }) => {
  const [visible, setVisible] = useState<boolean>(false);
  const handleDelete = () => {
    onDelete(banner.id);
  };
  return (
    <div key={banner.id} className="banner">
      <Image
        preview={{ visible: false }}
        alt={banner.title || ""}
        src={banner.picture.main}
        onClick={() => setVisible(true)}
      />

      <div style={{ display: "none" }}>
        <Image.PreviewGroup
          preview={{ visible, onVisibleChange: (vis) => setVisible(vis) }}
        >
          <Image src={banner.picture.main} />
          {banner.picture.mobile && <Image src={banner.picture.mobile} />}
        </Image.PreviewGroup>
      </div>

      <Popconfirm
        title="Вы уверены, что хотите удалить?"
        onConfirm={handleDelete}
        okText="Да"
        cancelText="Нет"
      >
        <DeleteTwoTone twoToneColor="#dc4234" />
      </Popconfirm>
    </div>
  );
};

export { Banner };
