import { FC } from "react";
import Layout from "../../components/layout";
import BaseProfile from "../../components/profile";
import BaseCoupon from "../../components/profile/Coupon";
import { useFetchCouponsQuery } from "../../lib/couponService";

const Coupon: FC = () => {
  const { data } = useFetchCouponsQuery();

  return (
    <Layout title="Купоны - Сеть аптек 120/80">
      <BaseProfile>
        <h4>Купоны</h4>
        {data && <BaseCoupon data={data} />}
      </BaseProfile>
    </Layout>
  );
};

export default Coupon;
