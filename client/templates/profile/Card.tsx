import { FC, MouseEvent } from "react";
import { useBlockCardMutation } from "../../lib/cardService";
import { ICard } from "../../models/ICard";
import styles from "./Table.module.scss";

type Props = {
  data: ICard[];
  className?: string;
};

const getStatusCard = (status: number) => {
  switch (status) {
    case 1:
      return "Новый";
    case 2:
      return "Активный";
    case 3:
      return "Блокирована";
    case 5:
      return "Закрыта";
    case 6:
      return "Завершена";
  }
};

const Card: FC<Props> = ({ data, className }) => {
  const [blockCard] = useBlockCardMutation();
  let classes = [styles.table];
  if (className) classes = classes.concat(className.split(" "));

  const handleBlock = (e: MouseEvent, cardId: string) => {
    e.preventDefault();
    blockCard(cardId);
  };

  return (
    <table className={classes.join(" ")}>
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
            <td>{getStatusCard(item.statusCode)}</td>
            <td>{item.expiryDate.format("L")}</td>
            <td>{item.cardType}</td>
            <td>
              {item.statusCode === 1 || item.statusCode === 2 ? (
                <a href="#" onClick={(e) => handleBlock(e, item.id)}>
                  Заблокировать
                </a>
              ) : (
                "Активировать"
              )}
            </td>
          </tr>
        ))}
      </tbody>
    </table>
  );
};

export default Card;
