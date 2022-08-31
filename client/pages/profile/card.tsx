import { FC } from "react";
import Layout from "../../components/layout";
import Profile from "../../components/profile";
import BaseCard from "../../components/profile/Card";
import { useFetchCardsQuery } from "../../lib/cardService";

const Card: FC = () => {
  const { data } = useFetchCardsQuery();

  return (
    <Layout>
      <Profile title="Список карт">{data && <BaseCard data={data} />}</Profile>
    </Layout>
  );
};

export default Card;
