import { useRouter } from "next/router";
import { FC } from "react";
import Layout from "../../components/layout";
import BaseProfile from "../../components/profile";
import BaseCoupon from "../../components/profile/Coupon";
import { useAuth } from "../../hooks/useAuth";

const Coupon: FC = () => {
  const { user } = useAuth();
  const router = useRouter();

  console.log(user);

  return (
    <Layout>
      <BaseProfile>
        <h4>Купоны</h4>
        <BaseCoupon data={[]} />
      </BaseProfile>
    </Layout>
  );
};

export default Coupon;
