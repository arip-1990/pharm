import { FC } from "react";
import { ICoupon } from "../../models/ICoupon";
import styles from "./Table.module.scss";

type Props = {
  data: ICoupon[];
  className?: string;
};

const Coupon: FC<Props> = ({ data, className }) => {
  let classes = [styles.table];
  if (className) classes = classes.concat(className.split(" "));

  return (
    <table className={classes.join(" ")}>
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
            <td>{item.number}</td>
            <td>{item.actualStart.format("d.m.Y")}</td>
            <td>{item.actualEnd.format("d.m.Y")}</td>
            <td>{item.statusType}</td>
            <td>{item.description}</td>
          </tr>
        ))}
      </tbody>
    </table>
  );
};

export default Coupon;
