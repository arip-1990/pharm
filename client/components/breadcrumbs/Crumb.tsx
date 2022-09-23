import Link from "next/link";
import { FC, HTMLAttributes, useState, useEffect } from "react";

interface Props extends HTMLAttributes<HTMLElement> {
  text?: string;
  textGenerator?: () => string;
  href: string;
  last?: boolean;
}

const Crumb: FC<Props> = ({
  text: defaultText,
  textGenerator,
  href,
  last = false,
}) => {
  const [text, setText] = useState(defaultText);

  useEffect(() => {
    if (!Boolean(textGenerator)) return;

    const finalText = textGenerator();
    setText(finalText);
  }, [textGenerator]);

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
