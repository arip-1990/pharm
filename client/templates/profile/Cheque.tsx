import { FC } from "react";
import { ICheque } from "../../models/ICheque";
import styles from "./Table.module.scss";

type Props = {
  data: ICheque[];
  className?: string;
};

const Cheque: FC<Props> = ({ data, className }) => {
  let classes = [styles.table];
  if (className) classes = classes.concat(className.split(" "));

  return (
    <table className={classes.join(" ")}>
      <colgroup>
        <col style={{ width: 110 }} />
      </colgroup>
      <thead>
        <tr>
          <th>Дата чека</th>
          <th>Номер чека</th>
          <th>Начислено бонусов</th>
          <th>Списано бонусов</th>
          <th>Сумма чека</th>
          <th>Сумма чека со скидкой</th>
        </tr>
      </thead>
      <tbody>
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
      </tbody>
    </table>
  );
};

export default Cheque;
