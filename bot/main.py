import os
import json
import redis
import threading
from telebot import TeleBot


bot = TeleBot('5264546096:AAHH7gzUFdZim4deJs0o78RwZ8x8q6vC6Io')
r = redis.Redis(os.getenv('REDIS_HOST', 'localhost'), 6379, decode_responses=True)
update = {'chat': None, 'command': None}
test_command = False
admin = 1195813156


@bot.message_handler(commands=['start'])
def welcome(message) -> None:
    user_name = f'{message.from_user.first_name} {message.from_user.last_name}' if message.from_user.last_name else message.from_user.first_name
    bot.reply_to(message, f'Привет {user_name}!')


@bot.message_handler(commands=['id'])
def help(message) -> None:
    bot.send_message(message.chat.id, message.from_user.id)


@bot.message_handler(commands=['update_category', 'update_product', 'update_store', 'update_offer'])
def update_data(message) -> None:
    global update
    send_message = ''

    if update['command']:
        if update['command'] == 'category':
            send_message = 'Обновление категории не завершено'
        elif update['command'] == 'product':
            send_message = 'Обновление товаров не завершено'
        elif update['command'] == 'store':
            send_message = 'Обновление аптек не завершено'
        elif update['command'] == 'offer':
            send_message = 'Обновление остатков не завершено'
        elif update['command'] == 'test':
            send_message = 'Обработка запроса не завершено'

        bot.reply_to(message, send_message)
    else:
        update['command'] = message.text.split(' ')[0].split('_')[1].strip()
        if update['command'] == 'category':
            send_message = 'Обновляем категории...'
        elif update['command'] == 'product':
            send_message = 'Обновляем товары...'
        elif update['command'] == 'store':
            send_message = 'Обновляем аптеки...'
        elif update['command'] == 'offer':
            send_message = 'Обновляем остатки...'

        update['chat'] = message.chat.id
        r.publish('update', json.dumps({'type': update['command']}))
        bot.send_message(message.chat.id, send_message)


@bot.message_handler(commands=['test'])
def test_data(message) -> None:
    global test_command

    if test_command:
        bot.reply_to(message, 'Обработка запроса не завершено!')
    else:
        test_command = True

        order_id = message.text.split(' ')[1].strip()
        r.publish('update', json.dumps({'chatId': message.chat.id, 'type': 'test', 'order': order_id}))
        bot.send_message(message.chat.id, 'Обрабатываем запрос...')


def handle_import(data: dict) -> None:
    global update

    if data['success']:
        message = data['message']
    else:
        message = f"Ошибка обработки запроса!\nФайл: {data['file']}\nСтрока: {data['line']}\n{data['message']}"

    bot.send_message(admin, message)
    # if update['chat'] and update['chat'] != admin:
    #     bot.send_message(update['chat'], message)

    update = {'chat': None, 'command': None}


def listen_redis() -> None:
    global update, test_command
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
                        update = {'chat': None, 'command': None}
                    elif type == 'pay':
                        for key, item in json.loads(message.get('data')):
                            bot.send_message(admin, f'{key} => {item}')
                    elif type == 'import':
                        handle_import(json.loads(message.get('data')))
                    elif type == 'test':
                        data = json.loads(message.get('data'))
                        bot.send_message(data['chatId'], data['message'])
                        test_command = False
                    else:
                        bot.send_message(admin, message.get('data'))
            except ValueError:
                bot.send_message(admin, message.get('data'))


def main():
    threading.Thread(target=listen_redis).start()

    bot.infinity_polling()


if __name__ == '__main__':
    main()
