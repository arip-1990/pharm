import { useRouter } from "next/router";
import { FC, useEffect } from "react";
import { useLocalStorage } from "react-use-storage";

import Layout from "../../../../templates";
import { ICard } from "../../../../models/ICard";

const CheckoutFailed: FC = () => {
  const [, , removeCarts] = useLocalStorage<ICard[]>("cart", []);
  const router = useRouter();
  const { id } = router.query;

  useEffect(() => removeCarts(), []);

  return (
    <Layout title="Ошибка оплаты - Сеть аптек 120/80">
      <h5 className="text-center">Заказ №{id}, не прошел оплату</h5>

      <div>
        <p>Произошла ошибка при оплате картой!</p>
      </div>
    </Layout>
  );
};

export default CheckoutFailed;
