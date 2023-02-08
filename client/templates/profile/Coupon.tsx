import { FC } from "react";
import { Table } from "../../components/table";
import { ICoupon } from "../../models/ICoupon";

type Props = {
  data: ICoupon[];
};

const Coupon: FC<Props> = ({ data }) => {
  return (
    <Table rounded striped>
      <tr>
        <th>Название купона</th>
        <th>Номер купона</th>
        <th>Начало действия</th>
        <th>Окончание действия</th>
        <th>Статус</th>
        <th>Описание</th>
      </tr>
      {data.map((item) => (
        <tr key={item.id}>
          <td>{item.name}</td>
          <td>{item.number}</td>
          <td>{item.actualStart.format("L")}</td>
          <td>{item.actualEnd.format("L")}</td>
          <td>{item.statusType}</td>
          <td>{item.description}</td>
        </tr>
      ))}
    </Table>
  );
};

export default Coupon;
