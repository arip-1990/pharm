import { FC, MouseEvent, useEffect, useState } from "react";
import { ICart } from "../../models/ICart";
import {
  useAddCartMutation,
  useChangeCartMutation,
  useFetchCartQuery,
} from "../../lib/cartService";

type Props = {
  productId: string;
  style?: object;
};

const Cart: FC<Props> = ({ productId, style }) => {
  const { data } = useFetchCartQuery();
  const [addCart] = useAddCartMutation();
  const [changeCart] = useChangeCartMutation();
  const [cart, setCart] = useState<ICart>();

  useEffect(() => {
    if (data?.length) {
      data.forEach((item) => {
        if (item.product.id === productId) {
          setCart(item);
          return;
        }
      });
    }
  }, [data]);

  const handleChangeCart = async (e: MouseEvent<HTMLButtonElement>) => {
    e.preventDefault();
    const quantity =
      e.currentTarget.innerText === "+" ? cart.quantity + 1 : cart.quantity - 1;

    try {
      await changeCart({ id: productId, quantity }).unwrap();
      setCart((item) => ({ ...item, quantity }));
    } catch (error) {
      console.log(error);
    }
  };

  if (cart) {
    return (
      <div className="input-group input-product" style={style}>
        <button className="btn btn-outline-primary" onClick={handleChangeCart}>
          -
        </button>
        <input
          type="number"
          className="form-control input-number"
          min={1}
          max={10}
          value={cart.quantity}
        />
        <button className="btn btn-outline-primary" onClick={handleChangeCart}>
          +
        </button>
      </div>
    );
  }

  return (
    <a
      className="btn btn-primary"
      style={style}
      onClick={() => addCart(productId)}
    >
      Добавить в корзину
    </a>
  );
};

export default Cart;
