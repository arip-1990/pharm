import React from 'react';
import { Steps, Typography } from 'antd';
import { IStatus } from '../../models/IOrder';

interface PropsType {
  statuses: IStatus[];
  paymentType: number;
  deliveryType: number;
  full?: boolean;
}

const StatusStep: React.FC<PropsType> = ({statuses, paymentType, deliveryType, full}) => {
  const getFull = () => (
    <Steps size={full ? 'default' : 'small'} current={1}>
      <Steps.Step key={1} description='Заказ принят' />
      {paymentType ? <Steps.Step key={2} description='Оплата картой' /> : null}
      <Steps.Step key={3} description='Отправка email' />
      <Steps.Step key={4} description='Отправка в 1с' />
      <Steps.Step key={5} description='Заказ собран' />
      {deliveryType ? <>
        <Steps.Step key={6} description='Вызов доставки' />
        <Steps.Step key={7} description='Заказ получен в аптеке' />
      </> : null}
      <Steps.Step key={8} description='Заказ получен клиентом' />
    </Steps>
  );

  const getDefault = () => {
    let status = <Typography.Text type="success">Заказ принят</Typography.Text>;;
    statuses.forEach(item => {
      switch (item?.value) {
        case 'P':
          if (item.state === 1) status = <Typography.Text type="danger">Оплата картой</Typography.Text>;
          else if (item.state === 2) status = <Typography.Text type="success">Оплата картой</Typography.Text>;
          else status = <Typography.Text type="secondary">Оплата картой</Typography.Text>;
          break;
        case 'J':
          if (item.state === 1) status = <Typography.Text type="danger">Отправка email</Typography.Text>;
          else if (item.state === 2) status = <Typography.Text type="success">Отправка email</Typography.Text>;
          else status = <Typography.Text type="secondary">Отправка email</Typography.Text>;
          break;
        case 'I':
          if (item.state === 1) status = <Typography.Text type="danger">Отправка в 1с</Typography.Text>;
          else if (item.state === 2) status = <Typography.Text type="success">Отправка в 1с</Typography.Text>;
          else status = <Typography.Text type="secondary">Отправка в 1с</Typography.Text>;
          break;
        case 'H':
          if (item.state === 1) status = <Typography.Text type="danger">Заказ собран</Typography.Text>;
          else if (item.state === 2) status = <Typography.Text type="success">Заказ собран</Typography.Text>;
          else status = <Typography.Text type="secondary">Заказ собран</Typography.Text>;
          break;
        case 'G':
          if (item.state === 1) status = <Typography.Text type="danger">Вызов доставки</Typography.Text>;
          else if (item.state === 2) status = <Typography.Text type="success">Вызов доставки</Typography.Text>;
          else status = <Typography.Text type="secondary">Вызов доставки</Typography.Text>;
          break;
        case 'S':
          if (item.state === 1) status = <Typography.Text type="danger">Заказ получен в аптеке</Typography.Text>;
          else if (item.state === 2) status = <Typography.Text type="success">Заказ получен в аптеке</Typography.Text>;
          else status = <Typography.Text type="secondary">Заказ получен в аптеке</Typography.Text>;
          break;
        case 'F':
          if (item.state === 1) status = <Typography.Text type="danger">Заказ получен клиентом</Typography.Text>;
          else if (item.state === 2) status = <Typography.Text type="success">Заказ получен клиентом</Typography.Text>;
          else status = <Typography.Text type="secondary">Заказ получен клиентом</Typography.Text>;
          break;
      }
    });

    return status;
  };

  return full ? getFull() : getDefault();
}

export default StatusStep;
