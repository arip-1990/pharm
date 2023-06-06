import Link from "next/link";
import Image from "next/image";
import { FC, MouseEvent, useEffect, useState } from "react";
import { useLocalStorage } from "react-use-storage";
import classNames from "classnames";

import { IProduct } from "../../models/IProduct";
import defaultImage from "../../assets/images/default.png";
import { ICart } from "../../models/ICart";
import Recipe from "./Recipe";
import Favorite from "./Favorite";
import Price from "./Price";

import styles from "./Card.module.scss";

type Props = {
  product: IProduct;
};

const Card: FC<Props> = ({ product }) => {

  const [carts, setCarts] = useLocalStorage<ICart[]>("cart", []);
  const [inCart, setInCart] = useState<boolean>(false);

  useEffect(() => {
    setInCart(carts?.some((item) => item.product.id === product.id));
  }, [carts]);

  const handleAddCart = (e: MouseEvent<HTMLButtonElement>) => {
    e.preventDefault();

    if (!inCart) {
      setCarts([...carts, { product, quantity: 1 }]);
    }
  };

  return (
    <div
      className={styles.card}
      itemProp="itemListElement"
      itemScope
      itemType="https://schema.org/Product"
    >
      <div className={styles.card_image}>
        <Favorite product={product} />

        <Image
          itemProp="image"
          layout="fill"
          objectFit="contain"
          src={product.photos[0]?.url || defaultImage}
          alt={product.name}
        />

        <Recipe isRecipe={product.recipe} />

        {product.discount && (
          <div
            className={classNames(styles.card_discount, {
              [styles.card_discount__50]: Number(product.discount) === 50,
              [styles.card_discount__30]: Number(product.discount) === 30,
            })}
          />
        )}
      </div>

      <div className={styles.card_body}>
        <h6 className={styles.title}>
          <Link href={`/catalog/product/${product.slug}`}>
            <a itemProp="url">
              <span itemProp="name">{product.name}</span>
            </a>
          </Link>
        </h6>

        <div>
          <Price slug={product.slug} totalOffers={product.totalOffers} />

          <button
            className={styles.card_button}
            onClick={handleAddCart}
            disabled={inCart}
          >
            {inCart ? "Добавлено" : "Добавить в корзину"}
          </button>
        </div>
      </div>
    </div>
  );
};

export default Card;
