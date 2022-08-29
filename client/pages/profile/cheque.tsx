import { useRouter } from "next/router";
import { FC } from "react";
import Layout from "../../components/layout";
import BaseProfile from "../../components/profile";
import BaseCheque from "../../components/profile/Cheque";
import Bonus from "../../components/profile/Bonus";
import { useAuth } from "../../hooks/useAuth";

const Cheque: FC = () => {
  const { user } = useAuth();
  const router = useRouter();

  console.log(user);

  return (
    <Layout>
      <BaseProfile>
        <h4>Покупки</h4>
        <BaseCheque className="mb-3" data={[]} />
        <h4>Подарочные бонусы</h4>
        <Bonus data={[]} />
      </BaseProfile>
    </Layout>
  );
};

export default Cheque;
