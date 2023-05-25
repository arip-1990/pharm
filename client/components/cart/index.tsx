import { ChangeEvent, FC, MouseEvent, useEffect, useState } from "react";
import { useLocalStorage } from "react-use-storage";

import { ICart } from "../../models/ICart";
import { IProduct } from "../../models/IProduct";

type Props = {
  product: IProduct;
  style?: object;
};

const Cart: FC<Props> = ({ product, style }) => {
  const [carts, setCarts] = useLocalStorage<ICart[]>("cart", []);
  const [quantity, setQuantity] = useState<number>(0);

  useEffect(() => {
    if (carts.length) {
      carts.forEach((item) => {
        if (item.product.id === product.id) {
          setQuantity(item.quantity);
          return;
        }
      });
    }
  }, []);

  const changeCart = (item: number) => {
    let newCarts: ICart[] = [];
    if (item >= 0 && item <= 10) {
      if (item === 0) {
        newCarts = carts.filter((cart) => cart.product.id !== product.id);
      } else {
        newCarts = carts.map((cart) => {
          if (cart.product.id === product.id) cart.quantity = item;
          return cart;
        });
      }

      if (!quantity) newCarts.push({ product, quantity: item });
      setCarts(newCarts);
      setQuantity(item);
    }
  };

  const handleChange = (e: ChangeEvent<HTMLInputElement>) => {
    e.preventDefault();
    changeCart(Number(e.currentTarget.value));
  };

  const handleClickChange = (e: MouseEvent<HTMLButtonElement>) => {
    changeCart(e.currentTarget.innerText === "+" ? quantity + 1 : quantity - 1);
  };

  if (quantity > 0) {
    return (
      <div className="input-group input-product" style={style}>
        <button className="btn btn-outline-primary" onClick={handleClickChange}>
          -
        </button>
        <input
          type="number"
          className="form-control input-number"
          min={1}
          max={10}
          value={quantity}
          onChange={handleChange}
        />
        <button className="btn btn-outline-primary" onClick={handleClickChange}>
          +
        </button>
      </div>
    );
  }

  return (
    <a className="btn btn-primary" style={style} onClick={() => changeCart(1)}>
      Добавить в корзину
    </a>
  );
};

export default Cart;
