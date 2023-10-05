import asyncio
import logging

from aiogram import Bot, Dispatcher
from aiogram.enums.parse_mode import ParseMode
from aiogram.fsm.storage.memory import MemoryStorage

import config
from handlers import router


async def main():
    bot = Bot(token=config.BOT_TOKEN, parse_mode=ParseMode.HTML)
    dp = Dispatcher(storage=MemoryStorage())
    dp.include_router(router)
    await bot.delete_webhook(drop_pending_updates=True)
    await dp.start_polling(bot, allowed_updates=dp.resolve_used_update_types())


if __name__ == "__main__":
    logging.basicConfig(level=logging.INFO)
    asyncio.run(main())


# logging.basicConfig(level=logging.INFO)
# redis = Redis(host=os.getenv('REDIS_HOST', 'localhost'))
# bot = Bot(token=os.getenv('API_TOKEN', '6159397113:AAHCvG9cvmj4OdBfcnQrrblKiD2fVbhWvQI'))
# dp = Dispatcher(bot)
#
#
# class SendData(TypedDict):
#     user: types.User | None
#     command: str | None
#     message: str | None
#     time: float | None
#
#
# send_data: SendData = {'user': None, 'command': None, 'message': None, 'time': None}
# admin = 1195813156
# channel_notification = -1001619975317
#
#
# @dp.message_handler(commands=['start', 'help'])
# async def send_welcome(message: types.Message) -> None:
#     user_name = f'{message.from_user.first_name} {message.from_user.last_name}' if message.from_user.last_name else message.from_user.first_name
#     await message.reply(f'Привет {user_name}!')
#
#
# @dp.message_handler(commands=['import_category', 'import_product', 'import_store', 'import_offer', 'search_init', 'search_reindex'])
# async def send_api_data(message: types.Message) -> None:
#     global send_data
#
#     if send_data['command'] and (time.time() - send_data['time']) < 360:
#         if send_data['user']:
#             user_name = send_data['user'].first_name + (f" {send_data['user'].last_name}" if send_data['user'].last_name else '')
#             await message.reply(f"Процесс не завершен: {send_data['message']}\nЗапущен пользователем: {user_name}")
#         else:
#             await message.reply(f"Процесс не завершен: {send_data['message']}")
#     else:
#         send_data['time'] = time.time()
#         [type_api, send_data['command']] = message.text.split(' ')[0].strip('/').split('_')
#
#         if send_data['command'] == 'category':
#             send_data['message'] = 'Обновление категории'
#         elif send_data['command'] == 'product':
#             send_data['message'] = 'Обновление товаров'
#         elif send_data['command'] == 'store':
#             send_data['message'] = 'Обновление аптек'
#         elif send_data['command'] == 'offer':
#             send_data['message'] = 'Обновление остатков'
#         elif send_data['command'] == 'init':
#             send_data['message'] = 'Инициализация индекса товаров'
#         elif send_data['command'] == 'reindex':
#             send_data['message'] = 'Обновление индекса товаров'
#
#         send_data['user'] = message.from_user
#
#         await redis.publish(f'api:{type_api}', ujson.dumps({'type': send_data['command']}))
#         await message.answer(f"Запущен процесс: {send_data['message']}")
#
#         user_name = message.from_user.first_name + (f' {message.from_user.last_name}' if message.from_user.last_name else '')
#
#         if os.getenv('APP_ENV', 'prod') == 'prod':
#             await bot.send_message(channel_notification, f"{user_name}: {send_data['message']}")
#
#
# async def handle_api_info(message: str) -> None:
#     if os.getenv('APP_ENV', 'prod') == 'prod':
#         await bot.send_message(channel_notification, message)
#     else:
#         await bot.send_message(admin, message)
#
#
# async def handle_api_error(data: dict) -> None:
#     if os.getenv('APP_ENV', 'prod') == 'prod':
#         await bot.send_message(channel_notification, f"File: {data['file']}\nMessage: {data['message']}")
#     else:
#         await bot.send_message(admin, f"File: {data['file']}\nMessage: {data['message']}")
#
#
# async def handle_api_send_data(data: dict) -> None:
#     global send_data
#
#     if os.getenv('APP_ENV', 'prod') == 'prod':
#         senders = [channel_notification, send_data['user'].id] if send_data['user'] else [channel_notification]
#     else:
#         senders = [admin]
#
#     if send_data['user'] and not data['success']:
#         for sender in senders:
#             await bot.send_message(sender, 'Не удалось выполнить ваш запрос!')
#
#     for sender in senders:
#         await bot.send_message(sender, data['message'])
#
#     send_data = {'user': None, 'command': None, 'message': None, 'time': None}
#
#
# if __name__ == '__main__':
#     asyncio.get_event_loop().create_task(listen_messages())
#
#     executor.start_polling(dp, skip_updates=True)
