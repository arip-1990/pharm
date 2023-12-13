import ujson
from aiogram import Bot
from config import config


redis = Redis(host=config.REDIS_HOST)


async def handle_api_info(bot: Bot, message: str) -> None:
    if config.APP_ENV == 'prod':
        await bot.send_message(config.CHANNEL, message)
    else:
        await bot.send_message(config.ADMIN_ID, message)


async def handle_api_error(bot: Bot, data: dict) -> None:
    if config.APP_ENV == 'prod':
        await bot.send_message(config.CHANNEL, f"File: {data['file']}\nMessage: {data['message']}")
    else:
        await bot.send_message(config.ADMIN_ID, f"File: {data['file']}\nMessage: {data['message']}")


# async def api_sender(api_type: str, command: str):
#     await redis.publish(f'api:{api_type}', ujson.dumps({'type': command}))
