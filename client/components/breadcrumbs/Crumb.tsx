import Link from "next/link";
import { FC } from "react";

interface Props {
  text: string;
  href: string;
  last?: boolean;
}

const Crumb: FC<Props> = ({text, href, last = false}) => {
  if (last) {
    return (
      <li className="breadcrumb-item active" aria-current="page">
        {text}
      </li>
    );
  }

  return (
    <li className="breadcrumb-item">
      <Link href={href}>
        <a>{text}</a>
      </Link>
    </li>
  );
};

export default Crumb;
