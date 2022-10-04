import React from "react";
import { Typography } from "antd";
import { IStatus } from "../../models/IOrder";
import classnames from "classnames";

interface PropsType {
  statuses: IStatus[];
  paymentType: string;
  deliveryType: string;
  full?: boolean;
}

const StatusStep: React.FC<PropsType> = ({
  statuses,
  paymentType,
  deliveryType,
  full,
}) => {
  const checkStatus = (status: string) =>
    statuses.some((item) => item.value === status && item.state === 2)
      ? "active"
      : statuses.some((item) => item.value === status && item.state === 1)
      ? "error"
      : "";

  const getFull = () => {
    const delivery = deliveryType === "delivery";
    const payment = paymentType === "card";

    return (
      <ul className={classnames("progressbar", { delivery, sber: payment })}>
        <li className="active">Заказ принят</li>
        {payment ? <li className={checkStatus("P")}>Оплата картой</li> : null}
        <li className={checkStatus("J")}>Отправка email</li>
        <li className={checkStatus("I")}>Отправка в 1с</li>
        <li className={checkStatus("H")}>Заказ собран</li>
        {delivery ? (
          <>
            <li className={checkStatus("G")}>Вызов доставки</li>
            <li className={checkStatus("S")}>Заказ получен в аптеке</li>
          </>
        ) : null}
        <li className={checkStatus("F")}>Заказ получен клиентом</li>
      </ul>
    );
  };

  const getDefault = () => {
    let status = <Typography.Text type="success">Заказ принят</Typography.Text>;
    statuses.forEach((item) => {
      switch (item?.value) {
        case "P":
          if (item.state === 1)
            status = (
              <Typography.Text type="danger">Оплата картой</Typography.Text>
            );
          else if (item.state === 2)
            status = (
              <Typography.Text type="success">Оплата картой</Typography.Text>
            );
          else
            status = (
              <Typography.Text type="secondary">Оплата картой</Typography.Text>
            );
          break;
        case "J":
          if (item.state === 1)
            status = (
              <Typography.Text type="danger">Отправка email</Typography.Text>
            );
          else if (item.state === 2)
            status = (
              <Typography.Text type="success">Отправка email</Typography.Text>
            );
          else
            status = (
              <Typography.Text type="secondary">Отправка email</Typography.Text>
            );
          break;
        case "I":
          if (item.state === 1)
            status = (
              <Typography.Text type="danger">Отправка в 1с</Typography.Text>
            );
          else if (item.state === 2)
            status = (
              <Typography.Text type="success">Отправка в 1с</Typography.Text>
            );
          else
            status = (
              <Typography.Text type="secondary">Отправка в 1с</Typography.Text>
            );
          break;
        case "H":
          if (item.state === 1)
            status = (
              <Typography.Text type="danger">Заказ собран</Typography.Text>
            );
          else if (item.state === 2)
            status = (
              <Typography.Text type="success">Заказ собран</Typography.Text>
            );
          else
            status = (
              <Typography.Text type="secondary">Заказ собран</Typography.Text>
            );
          break;
        case "G":
          if (item.state === 1)
            status = (
              <Typography.Text type="danger">Вызов доставки</Typography.Text>
            );
          else if (item.state === 2)
            status = (
              <Typography.Text type="success">Вызов доставки</Typography.Text>
            );
          else
            status = (
              <Typography.Text type="secondary">Вызов доставки</Typography.Text>
            );
          break;
        case "S":
          if (item.state === 1)
            status = (
              <Typography.Text type="danger">
                Заказ получен в аптеке
              </Typography.Text>
            );
          else if (item.state === 2)
            status = (
              <Typography.Text type="success">
                Заказ получен в аптеке
              </Typography.Text>
            );
          else
            status = (
              <Typography.Text type="secondary">
                Заказ получен в аптеке
              </Typography.Text>
            );
          break;
        case "F":
          if (item.state === 1)
            status = (
              <Typography.Text type="danger">
                Заказ получен клиентом
              </Typography.Text>
            );
          else if (item.state === 2)
            status = (
              <Typography.Text type="success">
                Заказ получен клиентом
              </Typography.Text>
            );
          else
            status = (
              <Typography.Text type="secondary">
                Заказ получен клиентом
              </Typography.Text>
            );
          break;
      }
    });

    return status;
  };

  return full ? getFull() : getDefault();
};

export default StatusStep;
