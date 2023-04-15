import { FC } from "react";
import Layout from "../../templates";
import BaseProfile from "../../templates/profile";
import BaseCheque from "../../templates/profile/Cheque";
import Bonus from "../../templates/profile/Bonus";
import { useFetchChequesQuery } from "../../lib/chequeService";
import { useFetchBonusesQuery } from "../../lib/bonusService";

const Cheque: FC = () => {
  const { data: cheques } = useFetchChequesQuery();
  const { data: bonuses } = useFetchBonusesQuery();

  return (
    <Layout title="Покупки - Сеть аптек 120/80">
      <BaseProfile title="Покупки">
        {cheques && <BaseCheque data={cheques} />}
        <h5>Бонусы</h5>
        {bonuses && <Bonus data={bonuses} />}
      </BaseProfile>
    </Layout>
  );
};

export default Cheque;
