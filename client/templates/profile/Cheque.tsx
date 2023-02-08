import { FC } from "react";
import { Table } from "../../components/table";
import { ICheque } from "../../models/ICheque";

type Props = {
  data: ICheque[];
};

const Cheque: FC<Props> = ({ data }) => {
  return (
    <Table rounded striped>
      <tr>
        <th>Дата чека</th>
        <th>Номер чека</th>
        <th>Начислено бонусов</th>
        <th>Списано бонусов</th>
        <th>Сумма чека</th>
        <th>Сумма чека со скидкой</th>
      </tr>
      {data.map((item) => (
        <tr key={item.id}>
          <td>{item.date.format("L")}</td>
          <td>{item.number}</td>
          <td>{item.bonus}</td>
          <td>{item.paidByBonus}</td>
          <td>{item.summ}</td>
          <td>{item.summDiscounted}</td>
        </tr>
      ))}
    </Table>
  );
};

export default Cheque;
