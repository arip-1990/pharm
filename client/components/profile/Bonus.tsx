import { FC } from "react";
import { IBonus } from "../../models/IBonus";

type Props = {
  data: IBonus[];
  className?: string;
};

const Bonus: FC<Props> = ({ data, className }) => {
  return (
    <table className={className}>
      <colgroup>
        <col style={{ border: "1px solid #ccc", padding: "0.5rem" }} span={5} />
      </colgroup>
      <thead>
        <tr>
          <th>Дата операции</th>
          <th>Компания</th>
          <th>Начислено бонусов</th>
          <th>Дата активации</th>
          <th>Дата сгорания</th>
        </tr>
      </thead>
      <tbody>
        {data.map((item) => (
          <tr key={item.id}>
            <td>{item.createdAt.format("d.m.Y")}</td>
            <td>{item.company}</td>
            <td>{item.accruedBonuses}</td>
            <td>{item.activatedAt.format("d.m.Y")}</td>
            <td>{item.expiredAt.format("d.m.Y")}</td>
          </tr>
        ))}
      </tbody>
    </table>
  );
};

export default Bonus;
