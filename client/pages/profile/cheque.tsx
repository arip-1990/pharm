import { FC } from "react";
import Layout from "../../components/layout";
import BaseProfile from "../../components/profile";
import BaseCheque from "../../components/profile/Cheque";
import Bonus from "../../components/profile/Bonus";
import { useFetchChequesQuery } from "../../lib/chequeService";
import { useFetchBonusesQuery } from "../../lib/bonusService";

const Cheque: FC = () => {
  const { data: cheques } = useFetchChequesQuery();
  const { data: bonuses } = useFetchBonusesQuery();

  return (
    <Layout title="Покупки - Сеть аптек 120/80">
      <BaseProfile>
        <h4>Покупки</h4>
        {cheques && <BaseCheque className="mb-3" data={cheques} />}
        <h4>Бонусы</h4>
        {bonuses && <Bonus data={bonuses} />}
      </BaseProfile>
    </Layout>
  );
};

export default Cheque;
