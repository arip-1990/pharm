import os
import ujson
import logging
import asyncio
from redis.asyncio import Redis
from aiogram import Bot, Dispatcher, executor, types


logging.basicConfig(level=logging.INFO)
redis = Redis(host=os.getenv('REDIS_HOST', 'localhost'))
bot = Bot(token=os.getenv('API_TOKEN', '6159397113:AAHCvG9cvmj4OdBfcnQrrblKiD2fVbhWvQI'))
dp = Dispatcher(bot)


import_data = {'chat': None, 'command': None}
search_data = {'chat': None, 'command': None}
admin = 1195813156
channel_notification = -1001619975317


@dp.message_handler(commands=['start', 'help'])
async def send_welcome(message: types.Message) -> None:
    user_name = f'{message.from_user.first_name} {message.from_user.last_name}' if message.from_user.last_name else message.from_user.first_name
    await message.reply(f'Привет {user_name}!')


@dp.message_handler(commands=['id'])
async def help(message: types.Message) -> None:
    await message.answer(message.from_user.id)


@dp.message_handler(commands=['update_category', 'update_product', 'update_store', 'update_offer'])
async def update_data(message: types.Message) -> None:
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

        await message.reply(send_message)
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
        await redis.publish('api:import', ujson.dumps({'type': import_data['command']}))
        await message.answer(send_message)
        await bot.send_message(channel_notification, send_message)


@dp.message_handler(commands=['search_init', 'search_reindex'])
async def search_service(message: types.Message) -> None:
    global search_data
    send_message = ''

    if search_data['command']:
        if search_data['command'] == 'init':
            send_message = 'Инициализация индекса товаров не завершено'
        elif search_data['command'] == 'reindex':
            send_message = 'Обновление индекса товаров не завершено'
        else:
            search_data = {'chat': None, 'command': None}
            send_message = 'Попробуйте повторить запрос пожалуйста))'

        await message.reply(send_message)
    else:
        search_data['command'] = message.text.split(' ')[0].split('_')[1].strip()
        if search_data['command'] == 'init':
            send_message = 'Инициализация индекса товаров..'
        elif search_data['command'] == 'reindex':
            send_message = 'Обновляем индексы товаров...'

        search_data['chat'] = message.chat.id
        await redis.publish('api:search', ujson.dumps({'type': search_data['command']}))
        await message.answer(send_message)
        await bot.send_message(channel_notification, send_message)

async def handle_api_info(message: str) -> None:
    await bot.send_message(channel_notification, message)


async def handle_api_error(data: dict) -> None:
    await bot.send_message(channel_notification, f"File: {data['file']}\nMessage: {data['message']}")


async def handle_import(data: dict) -> None:
    global import_data

    senders = [channel_notification, import_data['chat']] if import_data['chat'] else [channel_notification]
    if not data['success']:
        for sender in senders:
            await bot.send_message(sender, "Не удалось обновить данные!")

    for sender in senders:
        await bot.send_message(sender, data['message'])

    import_data = {'chat': None, 'command': None}


async def handle_search(data: dict) -> None:
    global search_data

    senders = [channel_notification, search_data['chat']] if search_data['chat'] else [channel_notification]
    if not data['success']:
        for sender in senders:
            await bot.send_message(sender, "Не удалось обновить данные!")

    for sender in senders:
        await bot.send_message(sender, data['message'])

    search_data = {'chat': None, 'command': None}


async def listen_messages():
    print('starting listener for redis')

    async with redis.pubsub() as pubsub:
        await pubsub.psubscribe('bot:*')

        try:
            while True:
                message = await pubsub.get_message()
                if message is not None and message.get('type') == 'pmessage':
                    channel = message.get('channel').decode('utf8').split(':')[-1]
                    data = message.get('data').decode('utf8')

                    if channel == 'import':
                        await handle_import(ujson.loads(data))
                    elif channel == 'search':
                        await handle_search(ujson.loads(data))
                    elif channel == 'info':
                        await handle_api_info(data)
                    elif channel == 'error':
                        await handle_api_error(ujson.loads(data))
                    else:
                        await bot.send_message(channel_notification, data)
        except Exception as e:
            print(e)
            await bot.send_message(channel_notification, e)


if __name__ == '__main__':
    asyncio.get_event_loop().create_task(listen_messages())

    executor.start_polling(dp, skip_updates=True)
