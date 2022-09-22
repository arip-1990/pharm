import { FC } from "react";

import styles from "./Card.module.scss";

interface Props {
  isRecipe: boolean;
}

const Recipe: FC<Props> = ({ isRecipe }) => {
  const classess = [styles.card_mod];
  classess.push(
    isRecipe ? styles.card_mod__prescription : styles.card_mod__delivery
  );

  return (
    <div className={classess.join(" ")}>
      <div className={styles.icon} />
      <div className={styles.text}>{isRecipe ? "По рецепту" : "Доставка"}</div>
    </div>
  );
};

export default Recipe;
