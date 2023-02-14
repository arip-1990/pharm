import { FC } from "react";
import Layout from "../../templates";
import Profile from "../../templates/profile";
import BaseCard from "../../templates/profile/Card";
import { useFetchCardsQuery } from "../../lib/cardService";

const Card: FC = () => {
  const { data } = useFetchCardsQuery();

  return (
    <Layout title="Список карт - Сеть аптек 120/80">
      <Profile title="Список карт">{data && <BaseCard data={data} />}</Profile>
    </Layout>
  );
};

export default Card;
