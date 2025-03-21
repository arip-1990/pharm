import { FC, ReactNode } from "react";

type Props = {
  children?: ReactNode;
  title?: string;
  style?: object;
  className?: string;
};

const Page: FC<Props> = ({ children, title, className, style = {} }) => {
  let classes = ["page"];
  if (className) classes = classes.concat(className.split(" "));

  return (
    <>
      {title && <h5 className="text-center">{title}</h5>}
      <div className={classes.join(" ")} style={style}>
        {children}
      </div>
    </>
  );
};

export default Page;
