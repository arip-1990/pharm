import { FC } from "react";
import { Table } from "../../components/table";
import { IBonus } from "../../models/IBonus";

type Props = {
  data: IBonus[];
};

const Bonus: FC<Props> = ({ data }) => {
  return (
    <Table rounded striped>
      <tr>
        <th>Дата операции</th>
        <th>Начислено бонусов</th>
        <th>Дата активации</th>
        <th>Дата сгорания</th>
      </tr>
      {data.map((item) => (
        <tr key={item.id}>
          <td>{item.createdDate.format("L")}</td>
          <td>{item.debet}</td>
          <td>{item.actualStart.format("L")}</td>
          <td>{item.actualEnd.format("L")}</td>
        </tr>
      ))}
    </Table>
  );
};

export default Bonus;
