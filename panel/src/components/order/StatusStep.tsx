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
  const checkStatus = (
    status: string,
    state?: "success" | "error" | "wait"
  ) => {
    if (state) {
      const stateNum = state === "success" ? 2 : state === "error" ? 1 : 0;

      return statuses.some(
        (item) => item.value === status && item.state === stateNum
      );
    }

    return statuses.some((item) => item.value === status);
  };

  const getClassnameByStatus = (status: string) =>
    checkStatus(status, "success")
      ? "active"
      : checkStatus(status, "error")
      ? "error"
      : "";

  if (full) {
    const delivery = deliveryType === "delivery";
    const payment = paymentType === "card";

    return (
      <ul
        className={classnames("progressbar", {
          delivery,
          sber: payment,
          cancelled: checkStatus("R"),
        })}
      >
        <li className="active">Заказ принят</li>
        {payment ? (
          <li className={getClassnameByStatus("P")}>Оплата картой</li>
        ) : null}
        <li className={getClassnameByStatus("M")}>Отправка почты</li>
        <li className={getClassnameByStatus("S")}>Отправка в 1с</li>
        <li className={getClassnameByStatus("H")}>Заказ собран</li>
        {delivery ? (
          <li className={getClassnameByStatus("D")}>Вызов доставки</li>
        ) : null}
        <li className={getClassnameByStatus("F")}>Заказ получен клиентом</li>
      </ul>
    );
  }

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
      case "M":
        if (item.state === 1)
          status = (
            <Typography.Text type="danger">Отправка почты</Typography.Text>
          );
        else if (item.state === 2)
          status = (
            <Typography.Text type="success">Отправка почты</Typography.Text>
          );
        else
          status = (
            <Typography.Text type="secondary">Отправка почты</Typography.Text>
          );
        break;
      case "S":
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
      case "D":
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
      case "R":
        status = <Typography.Text type="danger">Заказ отменен</Typography.Text>;
    }
  });

  return status;
};

export default StatusStep;
