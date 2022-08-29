import { useRouter } from "next/router";
import { FC } from "react";
import Layout from "../../components/layout";
import BaseProfile from "../../components/profile";
import Card from "../../components/profile/Card";
import { useAuth } from "../../hooks/useAuth";

const cardBlocking: FC = () => {
  const { user } = useAuth();
  const router = useRouter();

  console.log(user);

  return (
    <Layout>
      <BaseProfile title="Блокировка карты">
        <Card data={[]} />
      </BaseProfile>
    </Layout>
  );
};

export default cardBlocking;
