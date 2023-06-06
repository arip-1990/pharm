import { FC, useCallback, useEffect, useState } from "react";
import { useLocalStorage } from "react-use-storage";

import { IProduct } from "../../models/IProduct";

interface Props {
  product: IProduct;
}

const Favorite: FC<Props> = ({ product }) => {
  const [isFavorite, setIsFavorite] = useState<boolean>(false);
  const [favorites, setFavorites] = useLocalStorage<IProduct[]>(
    "favorites",
    []
  );

  useEffect(() => {
    setIsFavorite(favorites.some((item) => item.id === product.id));
  }, [favorites]);

  // const handleFavorite = useCallback(() => {
  //   if (isFavorite)
  //     setFavorites(favorites.filter((item) => item.id !== product.id));
  //   else setFavorites([...favorites, product]);
  // }, [isFavorite]);

  const handleFavorite = () => {
    if (isFavorite)
      setFavorites(favorites.filter((item) => item.id !== product.id));
    else setFavorites([...favorites, product]);
  }

  return (
    <i
      className={"icon-heart" + (isFavorite ? "" : "-empty")}
      onClick={handleFavorite}
    />
  );
};

export default Favorite;





