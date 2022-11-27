import { FC } from "react";
import Layout from "../../../components/layout";
import Profile from "../../../components/profile";
import Card from "../../../components/profile/Card";
import { useFetchCardsQuery } from "../../../lib/cardService";

const Block: FC = () => {
  const { data } = useFetchCardsQuery();

  return (
    <Layout title="Блокировка карты - Сеть аптек 120/80">
      <Profile title="Блокировка карты">{data && <Card data={data} />}</Profile>
    </Layout>
  );
};

export default Block;
