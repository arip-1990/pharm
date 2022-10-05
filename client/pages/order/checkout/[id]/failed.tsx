import { useRouter } from "next/router";
import { FC, useEffect } from "react";
import { useLocalStorage } from "react-use-storage";
import Layout from "../../../../components/layout";
import { ICard } from "../../../../models/ICard";

const CheckoutFailed: FC = () => {
  const [, , removeCarts] = useLocalStorage<ICard[]>("cart", []);
  const router = useRouter();
  const { id } = router.query;

  useEffect(() => removeCarts(), []);

  return (
    <Layout>
      <h1 className="text-center"></h1>

      <div>
        <p>
          Произошла ошибка при оплате картой!
        </p>
      </div>
    </Layout>
  );
};

export default CheckoutFailed;
