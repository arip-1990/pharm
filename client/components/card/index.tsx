import Link from "next/link";
import Image from "next/image";
import { IProduct } from "../../models/IProduct";
import defaultImage from "../../assets/images/default.png";
import { FC, MouseEvent, useCallback, useEffect, useState } from "react";
import { ICart } from "../../models/ICart";
import { useLocalStorage } from "react-use-storage";

import styles from "./Card.module.scss";
import classNames from "classnames";

const isRecipe = (recipe: boolean) => {
  const classess = [styles.card_mod];
  classess.push(
    recipe ? styles.card_mod__prescription : styles.card_mod__delivery
  );

  return (
    <div className={classess.join(" ")}>
      <div className={styles.icon} />
      <div className={styles.text}>{recipe ? "По рецепту" : "Доставка"}</div>
    </div>
  );
};

const isFavorite = (product: IProduct) => {
  const [isFavorite, setIsFavorite] = useState<boolean>(false);
  const [favorites, setFavorites] = useLocalStorage<IProduct[]>(
    "favorites",
    []
  );

  useEffect(() => {
    setIsFavorite(favorites.some((item) => item.id === product.id));
  }, [favorites]);

  const handleFavorite = useCallback(() => {
    if (isFavorite)
      setFavorites(favorites.filter((item) => item.id !== product.id));
    else setFavorites([...favorites, product]);
  }, [isFavorite]);

  return (
    <i
      className={"icon-heart" + (isFavorite ? "" : "-empty")}
      onClick={handleFavorite}
    />
  );
};

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
        {isFavorite(product)}
        <Image
          className="mt-2"
          itemProp="image"
          layout="fill"
          objectFit="contain"
          src={product.photos[0]?.url || defaultImage}
          alt={product.name}
        />
        {isRecipe(product.recipe)}
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
        <h6>
          <Link href={`/product/${product.slug}`}>
            <a itemProp="url">
              <span itemProp="name">{product.name}</span>
            </a>
          </Link>
        </h6>

        <div>
          {product.totalOffers ? (
            <>
              <p className={styles.card_marker}>
                <i className="fas fa-map-marker-alt" />
                {`В наличии в ${product.totalOffers} ` +
                  (product.totalOffers === 1 ? "аптеке" : "аптеках")}
              </p>
              <div
                itemProp="offers"
                itemScope
                itemType="https://schema.org/Offer"
              >
                <p className={styles["price-mask"]}>Показать цену</p>
                <p className={styles["price-real"]} itemProp="price">
                  от <span style={{ fontWeight: 600 }}></span> &#8381;
                </p>
              </div>
            </>
          ) : (
            <p className={styles.card_marker + " " + styles.card_marker__red}>
              <i className="fas fa-map-marker-alt" /> Нет в наличии
            </p>
          )}

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
