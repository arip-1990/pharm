import { NotificationManager } from "react-notifications";

type NotificationType = 'info' | 'success' | 'warning' | 'error';

type NotificationMessage = {
  title?: string;
  message: string;
}

export const useNotification = () => {
  const notification = (type: NotificationType = 'info', messages: NotificationMessage[] | string) => {
    if (typeof messages === 'string')
      NotificationManager[type](messages);
    else
      messages.forEach(item => NotificationManager[type](item.message, item?.title || ''));
  }

  return notification;
};
