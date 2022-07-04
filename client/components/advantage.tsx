import { FC, ReactNode } from "react";
import Image, { StaticImageData } from "next/image";

type Props = {
  children?: ReactNode;
  title: string;
  image?: string | StaticImageData;
};

const Advantage: FC<Props> = ({ children, title, image }) => {
  return (
    <div className="advantage">
      <h4 className="advantage_title">{title}</h4>
      {image && <Image src={image} />}
      <div className="advantage_text">{children}</div>
    </div>
  );
};

export default Advantage;
