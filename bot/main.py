import asyncio
import logging

from aiogram import Bot, Dispatcher
from aiogram.enums.parse_mode import ParseMode
from aiogram.fsm.storage.memory import MemoryStorage

import ujson
import utils
from config import config
from handlers import router


bot = Bot(token=config.BOT_TOKEN, parse_mode=ParseMode.HTML)


async def listen_messages():
    print('starting listener for redis')
    async with utils.redis.pubsub() as pubsub:
        await pubsub.psubscribe('bot:*')
        try:
            while True:
                message = await pubsub.get_message()
                if message is not None and message.get('type') == 'pmessage':
                    channel = message.get('channel').decode('utf8').split(':')[-1]
                    data = message.get('data').decode('utf8')

                    if channel in ['import', 'search']:
                        await utils.api_sender(channel, ujson.loads(data))
                    elif channel == 'info':
                        await utils.handle_api_info(bot, data)
                    elif channel == 'error':
                        await utils.handle_api_error(bot, ujson.loads(data))
                    else:
                        if config.APP_ENV == 'prod':
                            await bot.send_message(config.CHANNEL, data)
                        else:
                            await bot.send_message(config.ADMIN_ID, data)
        except Exception as e:
            print(e)
            if config.APP_ENV == 'prod':
                await bot.send_message(config.CHANNEL, e)
            else:
                await bot.send_message(config.ADMIN_ID, e)


async def main():
    dp = Dispatcher(storage=MemoryStorage())
    dp.include_router(router)
    await bot.delete_webhook(drop_pending_updates=True)
    await dp.start_polling(bot, allowed_updates=dp.resolve_used_update_types())


if __name__ == "__main__":
    logging.basicConfig(level=logging.INFO)
    asyncio.run(main())
