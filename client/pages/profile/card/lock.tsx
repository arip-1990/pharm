import { FC } from "react";

import Layout from "../../../templates";
import Profile from "../../../templates/profile";
import Card from "../../../templates/profile/Card";
import { useFetchCardsQuery } from "../../../lib/cardService";

const Lock: FC = () => {
  const { data } = useFetchCardsQuery();

  return (
    <Layout title="Блокировка карты - Сеть аптек 120/80">
      <Profile title="Блокировка карты">{data && <Card data={data} />}</Profile>
    </Layout>
  );
};

export default Lock;
