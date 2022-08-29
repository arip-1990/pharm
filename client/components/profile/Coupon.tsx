import { FC } from "react";
import { ICoupon } from "../../models/ICoupon";

type Props = {
  data: ICoupon[];
  className?: string;
};

const Coupon: FC<Props> = ({ data, className }) => {
  return (
    <table className={className}>
      <colgroup>
        <col style={{ border: "1px solid #ccc", padding: "0.5rem" }} span={6} />
      </colgroup>
      <thead>
        <tr>
          <th>Название купона</th>
          <th>Номер купона</th>
          <th>Начало действия</th>
          <th>Окончание действия</th>
          <th>Статус</th>
          <th>Описание</th>
        </tr>
      </thead>
      <tbody>
        {data.map((item) => (
          <tr key={item.id}>
            <td>{item.name}</td>
            <td>{item.id}</td>
            <td>{item.startedAt.format("d.m.Y")}</td>
            <td>{item.expiredAt.format("d.m.Y")}</td>
            <td>{item.status}</td>
            <td>{item.description}</td>
          </tr>
        ))}
      </tbody>
    </table>
  );
};

export default Coupon;
