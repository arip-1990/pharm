import json
import os
import redis
from telegram import Bot


bot = Bot('5237529924:AAGFckEDU3jv5e481Tzy0yzkudn8Xj03zKc')


def send_message(text: str) -> None:
    bot.send_message(1195813156, text)

def main():
    r = redis.Redis(os.getenv('REDIS_HOST', 'localhost'), 6379, decode_responses=True)
    p = r.pubsub()
    p.subscribe('bot')

    f = open('md/test.md', 'r')
    md = f.read()

    for message in p.listen():
        if message is not None and isinstance(message, dict):
            try:
                if message.get('type') == 'message':
                    data = json.loads(message.get('data'))
                    if isinstance(data, dict):
                        for k, item in data.items():
                            send_message(md.format(k, item))
                    else:
                        send_message(data)
            except:
                pass


if __name__ == '__main__':
    main()
