import { FC } from "react";
import { ICard } from "../../models/ICard";

type Props = {
  data: ICard[];
  className?: string;
};

const Card: FC<Props> = ({ data, className }) => {
  return (
    <table className={className}>
      <colgroup>
        <col style={{ border: "1px solid #ccc", padding: "0.5rem" }} span={4} />
      </colgroup>
      <thead>
        <tr>
          <th>Статус карты</th>
          <th>Дата окончания</th>
          <th>Тип карты</th>
          <th>Изменение состояния</th>
        </tr>
      </thead>
      <tbody>
        {data.map((item) => (
          <tr key={item.id}>
            <td>{item.statusCode}</td>
            <td>{item.expiryDate.format("d.m.Y")}</td>
            <td>{item.cardType}</td>
            <td>{"Активировать" || "Заблокировать"}</td>
          </tr>
        ))}
      </tbody>
    </table>
  );
};

export default Card;
