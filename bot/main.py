import os
import json
import redis
import threading
from telebot import TeleBot


bot = TeleBot('5264546096:AAHH7gzUFdZim4deJs0o78RwZ8x8q6vC6Io')
r = redis.Redis(os.getenv('REDIS_HOST', 'localhost'), 6379, decode_responses=True)
update_command = None


@bot.message_handler(commands=['start'])
def welcome(message) -> None:
    user_name = f'{message.from_user.first_name} {message.from_user.last_name}' if message.from_user.last_name else message.from_user.first_name
    bot.reply_to(message, f'Привет {user_name}!')


@bot.message_handler(commands=['id'])
def help(message) -> None:
    bot.send_message(message.chat.id, message.from_user.id)


@bot.message_handler(commands=['update_category', 'update_product', 'update_store', 'update_offer', 'update_test'])
def update_data(message) -> None:
    global update_command
    send_message = ''

    if update_command:
        if update_command == 'category':
            send_message = 'Обновление категории не завершено'
        elif update_command == 'product':
            send_message = 'Обновление товаров не завершено'
        elif update_command == 'store':
            send_message = 'Обновление аптек не завершено'
        elif update_command == 'offer':
            send_message = 'Обновление остатков не завершено'
        elif update_command == 'test':
            send_message = 'Обработка запроса не завершено'

        bot.reply_to(message, send_message)
    else:
        update_command = message.text.split(' ')[0].split('_')[1].strip()
        if update_command == 'category':
            send_message = 'Обновляем категории...'
        elif update_command == 'product':
            send_message = 'Обновляем товары...'
        elif update_command == 'store':
            send_message = 'Обновляем аптеки...'
        elif update_command == 'offer':
            send_message = 'Обновляем остатки...'
        elif update_command == 'test':
            send_message = 'Обрабатываем запрос...'

        r.publish('update', json.dumps({'chatId': message.chat.id, 'type': update_command}))
        bot.send_message(message.chat.id, send_message)


def listen_redis() -> None:
    global update_command
    p = r.pubsub()
    p.psubscribe('bot:*')

    for message in p.listen():
        if message is not None and isinstance(message, dict):
            try:
                if message.get('type') == 'pmessage':
                    type = message.get('channel').split(':')[1]
                    if type == 'update':
                        data = json.loads(message.get('data'))
                        bot.send_message(data['chatId'], data['message'])
                        update_command = None
                    elif type == 'pay':
                        for key, item in json.loads(message.get('data')):
                            bot.send_message(1195813156, f'{key} => {item}')
                    else:
                        bot.send_message(1195813156, message.get('data'))
            except ValueError:
                bot.send_message(1195813156, message.get('data'))


def main():
    threading.Thread(target=listen_redis).start()

    bot.infinity_polling()


if __name__ == '__main__':
    main()
