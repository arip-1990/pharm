import axios from "axios";
import classNames from "classnames";
import { FC, MouseEvent, useCallback, useState } from "react";

import { useNotification } from "../../hooks/useNotification";
import api from "../../lib/api";

import styles from "./Card.module.scss";

interface Props {
  slug: string;
  totalOffers: number;
}

const Price: FC<Props> = ({ slug, totalOffers }) => {
  const [price, setPrice] = useState<number>();
  const notification = useNotification();

  const handlePrice = useCallback(async (e: MouseEvent<HTMLElement>) => {
    e.preventDefault();
    try {
      const { data } = await api.get<number>(
        `/v1/catalog/product/${slug}/price`
      );
      setPrice(data);
    } catch (error) {
      if (axios.isAxiosError(error)) {
        notification("error", error.response?.data);
      }
      console.log(error);
    }
  }, []);

  if (!totalOffers) {
    return (
      <p className={styles.card_marker + " " + styles.card_marker__red}>
        <i className="icon-marker" /> Нет в наличии
      </p>
    );
  }

  return (
    <>
      <p className={styles.card_marker}>
        <i className="icon-marker" />
        {`В наличии в ${totalOffers} ` +
          (totalOffers === 1 ? "аптеке" : "аптеках")}
      </p>
      <div itemProp="offers" itemScope itemType="https://schema.org/Offer">
        <p
          className={classNames(
            styles.price,
            price === undefined ? styles.price_mask : styles.price_real
          )}
          itemProp="price"
          onClick={handlePrice}
        >
          {price === undefined ? (
            "Показать цену"
          ) : (
            <>
              от <span style={{ fontWeight: 600 }}>{price}</span> &#8381;
            </>
          )}
        </p>
      </div>
    </>
  );
};

export default Price;
