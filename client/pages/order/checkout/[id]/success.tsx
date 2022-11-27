import { useRouter } from "next/router";
import { FC, useEffect } from "react";
import { useLocalStorage } from "react-use-storage";
import Layout from "../../../../components/layout";
import { ICard } from "../../../../models/ICard";

const CheckoutSuccess: FC = () => {
  const [, , removeCarts] = useLocalStorage<ICard[]>("cart", []);
  const router = useRouter();
  const { id } = router.query;

  useEffect(() => removeCarts(), []);

  return (
    <Layout title="Заказ оформлен - Сеть аптек 120/80">
      <h1 className="text-center">Спасибо, заказ №{id} оформлен!</h1>

      <div>
        <p>
          Мы отправим электронное письмо с информацией о статусах вашего заказа
          на электронную почту (если вы ее указали).
        </p>
        <p>Когда заказ будет собран в аптеке, с вами свяжется оператор.</p>
        <p>
          В случае возникновения дополнительных вопросов о наличии товара или
          интервалов доставки с вами свяжется оператор.
        </p>
        <br />
        <p>Статус заказа можно проверить в личном кабинете или по телефону:</p>
        <b>+7 (8722) 606-366</b>
      </div>
    </Layout>
  );
};

export default CheckoutSuccess;
