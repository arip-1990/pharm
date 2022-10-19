import { FC } from "react";
import { IBonus } from "../../models/IBonus";
import styles from "./Table.module.scss";

type Props = {
  data: IBonus[];
  className?: string;
};

const Bonus: FC<Props> = ({ data, className }) => {
  let classes = [styles.table];
  if (className) classes = classes.concat(className.split(" "));

  return (
    <table className={classes.join(" ")}>
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
            <td>{item.createdDate.format("L")}</td>
            <td>{item.campaignName}</td>
            <td>{item.debet}</td>
            <td>{item.actualStart.format("L")}</td>
            <td>{item.actualEnd.format("L")}</td>
          </tr>
        ))}
      </tbody>
    </table>
  );
};

export default Bonus;
