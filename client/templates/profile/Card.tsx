import { FC, MouseEvent } from "react";
import { Table } from "../../components/table";
import { useBlockCardMutation } from "../../lib/cardService";
import { ICard } from "../../models/ICard";

type Props = {
  data: ICard[];
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

const Card: FC<Props> = ({ data }) => {
  const [blockCard] = useBlockCardMutation();

  const handleBlock = (e: MouseEvent, cardId: string) => {
    e.preventDefault();
    blockCard(cardId);
  };

  return (
    <Table rounded striped>
      <tr>
        <th>Статус карты</th>
        <th>Дата окончания</th>
        <th>Тип карты</th>
        <th>Изменение состояния</th>
      </tr>
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
    </Table>
  );
};

export default Card;
