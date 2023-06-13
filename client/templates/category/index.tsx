import { FC } from "react";
import Link from "next/link";

import { ICategory } from "../../models/ICategory";

const generateCategory = (category: ICategory) => {
  return (
    <li key={category.id}>
      <Link href={`/products/${category.slug}`}>
        <a>
          {category.picture && <img src={category.picture} alt="" />}
          {category.name}
        </a>
      </Link>
      {category.children.length ? (
        <div className="overlay">
          <ul>
            {category.children
              .filter((_, i) => i < 10)
              .map((item) => generateCategory(item))}
            {category.children.length > 10 ? (
              <li>
                <Link href={`/products/${category.slug}`}>
                  <a>{category.name}</a>
                </Link>
              </li>
            ) : null}
          </ul>
        </div>
      ) : null}
    </li>
  );
};

interface IProps {
  data: ICategory[];
}

const Category: FC<IProps> = ({ data }) => {
  return (
    <ul className="category">
      {/* <li className="sale">
              <a href="/catalog/sale">
                <Image width={36} height={36} src={saleImage} alt="" />
                Распродажа
              </a>
            </li> */}
      {data.map((item) => generateCategory(item))}
    </ul>
  );
};

export { Category };
