import redis
import ujson
from redis import Redis

import config


redis = Redis(host=config.REDIS_HOST)


async def send_command_api(type_api: str, command: str):
    print(f'Type: {type_api}, Command: {command}')
    # await redis.publish(f'api:{type_api}', ujson.dumps({'type': command}))

    #
    #         if os.getenv('APP_ENV', 'prod') == 'prod':
    #             await bot.send_message(channel_notification, f"{user_name}: {send_data['message']}")


# async def listen_messages():
#     print('starting listener for redis')
#     async with redis.pubsub() as pubsub:
#         await pubsub.psubscribe('bot:*')
#         try:
#             while True:
#                 message = await pubsub.get_message()
#                 if message is not None and message.get('type') == 'pmessage':
#                     channel = message.get('channel').decode('utf8').split(':')[-1]
#                     data = message.get('data').decode('utf8')
#
#                     if channel in ['import', 'search']:
#                         await handle_api_send_data(ujson.loads(data))
#                     elif channel == 'info':
#                         await handle_api_info(data)
#                     elif channel == 'error':
#                         await handle_api_error(ujson.loads(data))
#                     else:
#                         if os.getenv('APP_ENV', 'prod') == 'prod':
#                             await bot.send_message(channel_notification, data)
#                         else:
#                             await bot.send_message(admin, data)
#         except Exception as e:
#             print(e)
#             if os.getenv('APP_ENV', 'prod') == 'prod':
#                 await bot.send_message(channel_notification, e)
#             else:
#                 await bot.send_message(admin, e)
