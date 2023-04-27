import os
import json
import redis
from threading import Thread
from telebot import TeleBot


bot = TeleBot('5264546096:AAHH7gzUFdZim4deJs0o78RwZ8x8q6vC6Io')
r = redis.Redis(host=os.getenv('REDIS_HOST', 'localhost'))

import_data = {'chat': None, 'command': None}
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
        r.publish('api:import', json.dumps({'type': import_data['command']}))
        bot.send_message(message.chat.id, send_message)


def handle_api_info(message: str) -> None:
    bot.send_message(admin, message)


def handle_api_error(data: dict) -> None:
    bot.send_message(admin, f"File: {data['file']}\nMessage: {data['message']}")


def handle_import(data: dict) -> None:
    global import_data

    senders = [admin, import_data['chat']] if (import_data['chat'] and import_data['chat'] != admin) else [admin]
    if not data['success']:
        for sender in senders:
            bot.send_message(sender, "Не удалось обновить данные!")
    
    for sender in senders:
        bot.send_message(sender, data['message'])

    import_data = {'chat': None, 'command': None}


def listen_messages() -> None:
    p = r.pubsub()
    p.psubscribe('bot:*')

    for message in p.listen():
        if message and isinstance(message, dict):
            try:
                if message.get('type') == 'pmessage':
                    channel = message.get('channel').decode('utf8').split(':')[-1]
                    data = message.get('data').decode('utf8')

                    if channel == 'import':
                        handle_import(json.loads(data))
                    elif channel == 'info':
                        handle_api_info(data)
                    elif channel == 'error':
                        handle_api_error(json.loads(data))
                    else:
                        bot.send_message(admin, data)
            except Exception as e:
                bot.send_message(admin, e)


def main():
    Thread(target=listen_messages).start()

    bot.infinity_polling()


if __name__ == '__main__':
    main()
