import asyncio
import logging

from aiogram import Router, Bot, Dispatcher
from aiogram.types import Message
from aiogram.filters import CommandStart
from aiogram.enums.parse_mode import ParseMode
from aiogram.fsm.storage.memory import MemoryStorage
from aiogram_dialog import DialogManager, StartMode, setup_dialogs

from config import config
from states import MyState, dialog


bot = Bot(token=config.BOT_TOKEN.get_secret_value(), parse_mode=ParseMode.HTML)
router = Router()


# async def listen_messages():
#     print('starting listener for redis')
#     async with utils.redis.pubsub() as pubsub:
#         await pubsub.psubscribe('bot:*')
#         try:
#             while True:
#                 message = await pubsub.get_message()
#                 if message is not None and message.get('type') == 'pmessage':
#                     channel = message.get('channel').decode('utf8').split(':')[-1]
#                     data = message.get('data').decode('utf8')
#
#                     if channel in ['import', 'search']:
#                         await utils.api_sender(channel, ujson.loads(data))
#                     elif channel == 'info':
#                         await utils.handle_api_info(bot, data)
#                     elif channel == 'error':
#                         await utils.handle_api_error(bot, ujson.loads(data))
#                     else:
#                         if config.APP_ENV == 'prod':
#                             await bot.send_message(config.CHANNEL, data)
#                         else:
#                             await bot.send_message(config.ADMIN_ID, data)
#         except Exception as e:
#             print(e)
#             if config.APP_ENV == 'prod':
#                 await bot.send_message(config.CHANNEL, e)
#             else:
#                 await bot.send_message(config.ADMIN_ID, e)



@router.message(CommandStart())
async def start(message: Message, dialog_manager: DialogManager):
    # await message.answer(text.greet.format(user_name=message.from_user.full_name, user_id=message.from_user.id))
    # Important: always set `mode=StartMode.RESET_STACK` you don't want to stack dialogs
    await dialog_manager.start(MyState.main, mode=StartMode.RESET_STACK)



async def main():
    dp = Dispatcher(storage=MemoryStorage())
    dp.include_router(dialog)
    dp.include_router(router)
    setup_dialogs(dp)
    await bot.delete_webhook(drop_pending_updates=True)
    await dp.start_polling(bot, allowed_updates=dp.resolve_used_update_types())


if __name__ == "__main__":
    logging.basicConfig(level=logging.INFO)
    asyncio.run(main())
