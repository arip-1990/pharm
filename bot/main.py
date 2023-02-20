import os
import json
import redis
import threading
from telebot import TeleBot


bot = TeleBot('5264546096:AAHH7gzUFdZim4deJs0o78RwZ8x8q6vC6Io')
r = redis.Redis(os.getenv('REDIS_HOST', 'localhost'), 6379, 2, decode_responses=True)

import_data = {'chat': None, 'command': None}
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
    global import_data
    send_message = ''

    if import_data['command']:
        if import_data['command'] == 'category':
            send_message = 'Обновление категории не завершено'
        elif import_data['command'] == 'product':
            send_message = 'Обновление товаров не завершено'
        elif import_data['command'] == 'store':
            send_message = 'Обновление аптек не завершено'
        elif import_data['command'] == 'offer':
            send_message = 'Обновление остатков не завершено'
        else:
            import_data = {'chat': None, 'command': None}
            send_message = 'Попробуйте повторить запрос пожалуйста))'

        bot.reply_to(message, send_message)
    else:
        import_data['command'] = message.text.split(' ')[0].split('_')[1].strip()
        if import_data['command'] == 'category':
            send_message = 'Обновляем категории...'
        elif import_data['command'] == 'product':
            send_message = 'Обновляем товары...'
        elif import_data['command'] == 'store':
            send_message = 'Обновляем аптеки...'
        elif import_data['command'] == 'offer':
            send_message = 'Обновляем остатки...'

        import_data['chat'] = message.chat.id
        r.publish('update', json.dumps({'type': import_data['command']}))
        bot.send_message(message.chat.id, send_message)


def handle_import(data: dict) -> None:
    global import_data

    if data['success']:
        bot.send_message(admin, data['message'])

        if import_data['chat'] and import_data['chat'] != admin:
            bot.send_message(import_data['chat'], data['message'])
    else:
        handle_api_error(data)

    import_data = {'chat': None, 'command': None}


def handle_api_info(message: str) -> None:
    bot.send_message(admin, message)


def handle_api_error(data: dict) -> None:
    bot.send_message(admin, f"File: {data['file']}\nMessage: {data['message']}")


def listen_messages() -> None:
    global test_command
    r.psubscribe('bot:*')

    for message in r.listen():
        if message and isinstance(message, dict):
            try:
                if message.get('type') == 'pmessage':
                    type = message.get('channel').split(':')[1]
                    if type == 'info':
                        handle_api_info(message.get('data'))
                    elif type == 'error':
                        handle_api_error(json.loads(message.get('data')))
                    else:
                        bot.send_message(admin, message.get('data'))
            except ValueError:
                bot.send_message(admin, message.get('data'))


def main():
    threading.Thread(target=listen_messages).start()

    bot.infinity_polling()


if __name__ == '__main__':
    main()
