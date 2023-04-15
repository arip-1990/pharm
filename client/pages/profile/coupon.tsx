import { FC } from "react";
import Layout from "../../templates";
import BaseProfile from "../../templates/profile";
import BaseCoupon from "../../templates/profile/Coupon";
import { useFetchCouponsQuery } from "../../lib/couponService";

const Coupon: FC = () => {
  const { data } = useFetchCouponsQuery();

  return (
    <Layout title="Купоны - Сеть аптек 120/80">
      <BaseProfile title="Купоны">
        {data && <BaseCoupon data={data} />}
      </BaseProfile>
    </Layout>
  );
};

export default Coupon;
