import asyncio
import logging

from aiogram import Bot, Dispatcher
from aiogram.types import Message, ErrorEvent
from aiogram.filters import CommandStart, ExceptionTypeFilter
from aiogram.fsm.storage.memory import MemoryStorage

from aiogram_dialog import DialogManager, StartMode, ShowMode, setup_dialogs
from aiogram_dialog.api.exceptions import UnknownIntent, UnknownState

from dialogs import dialog
from config import config
from states import Main


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


async def start(message: Message, dialog_manager: DialogManager):
    await dialog_manager.start(Main.START, mode=StartMode.RESET_STACK)


async def on_unknown_intent(event: ErrorEvent, dialog_manager: DialogManager):
    """Example of handling UnknownIntent Error and starting new dialog."""
    logging.error("Restarting dialog: %s", event.exception)
    await dialog_manager.start(Main.START, mode=StartMode.RESET_STACK, show_mode=ShowMode.SEND)


async def on_unknown_state(event: ErrorEvent, dialog_manager: DialogManager):
    """Example of handling UnknownState Error and starting new dialog."""
    logging.error("Restarting dialog: %s", event.exception)
    await dialog_manager.start(Main.START, mode=StartMode.RESET_STACK, show_mode=ShowMode.SEND)


def setup_dp():
    dp = Dispatcher(storage=MemoryStorage())
    dp.message.register(start, CommandStart())
    dp.errors.register(on_unknown_intent, ExceptionTypeFilter(UnknownIntent))
    dp.errors.register(on_unknown_state, ExceptionTypeFilter(UnknownState))
    dp.include_router(dialog)
    setup_dialogs(dp)

    return dp


async def main():
    logging.basicConfig(level=logging.INFO)
    bot = Bot(token=config.BOT_TOKEN.get_secret_value())
    dp = setup_dp()

    await bot.delete_webhook(drop_pending_updates=True)
    await dp.start_polling(bot)


if __name__ == "__main__":
    asyncio.run(main())
