import { FC } from "react";
import { ICheque } from "../../models/ICheque";

type Props = {
  data: ICheque[];
  className?: string;
};

const Cheque: FC<Props> = ({ data, className }) => {
  return (
    <table className={className}>
      <colgroup>
        <col
          style={{ width: 110, border: "1px solid #ccc", padding: "0.5rem" }}
          span={8}
        />
      </colgroup>
      <thead>
        <tr>
          <th>Дата чека</th>
          <th>Номер чека</th>
          <th>Компания</th>
          <th>Магазин</th>
          <th>Начислено бонусов</th>
          <th>Списано бонусов</th>
          <th>Сумма чека</th>
          <th>Сумма чека со скидкой</th>
        </tr>
      </thead>
      <tbody>
        {data.map((item) => (
          <tr key={item.id}>
            <td>{item.createdAt.format("d.m.Y")}</td>
            <td>{item.id}</td>
            <td>{item.company}</td>
            <td>{item.store}</td>
            <td>{item.accruedBonuses}</td>
            <td>{item.deductedBonuses}</td>
            <td>{item.amount}</td>
            <td>{item.discountedAmount}</td>
          </tr>
        ))}
      </tbody>
    </table>
  );
};

export default Cheque;
