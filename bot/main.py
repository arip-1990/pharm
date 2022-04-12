import json
import os
import redis
import asyncio
from telegram import Bot, Update, ForceReply
from telegram.ext import Updater, CommandHandler, CallbackContext


# bot = Bot('5237529924:AAGFckEDU3jv5e481Tzy0yzkudn8Xj03zKc')
r = redis.Redis(os.getenv('REDIS_HOST', 'localhost'), 6379, decode_responses=True)


def start(update: Update, context: CallbackContext) -> None:
    user = update.effective_user
    update.message.reply_markdown_v2(
        fr'Привет {user.mention_markdown_v2()}\!',
        reply_markup=ForceReply(selective=True),
    )


def help_command(update: Update, context: CallbackContext) -> None:
    with open('md/help.md', 'r') as f:
        md = f.read()

    update.message.reply_html(md, reply_markup=ForceReply(selective=True))


def update_data(update: Update, context: CallbackContext) -> None:
    # r.publish('update', update.message.text[7:].strip())
    update.message.reply_text(update.message.text)
    update.message.reply_text(update.message.text[7:].strip())
    update.message.reply_text('Ваш запрос обрабатывается!')


def send_message(update: Update, text: str) -> None:
    update.message.reply_text(text)


async def listen_redis():
    p = r.pubsub()
    p.subscribe('bot')

    f = open('md/test.md', 'r')
    md = f.read()

    update = Update(1195813156)
    for message in p.listen():
        if message is not None and isinstance(message, dict):
            try:
                if message.get('type') == 'message':
                    data = json.loads(message.get('data'))
                    if isinstance(data, dict):
                        for k, item in data.items():
                            send_message(update, md.format(k, item))
                    else:
                        send_message(update, data)
            except:
                pass


def main():
    updater = Updater('5237529924:AAGFckEDU3jv5e481Tzy0yzkudn8Xj03zKc')

    loop = asyncio.get_event_loop()
    try:
        loop.run_until_complete(listen_redis())
    finally:
        pass

    dispatcher = updater.dispatcher

    dispatcher.add_handler(CommandHandler("start", start))
    dispatcher.add_handler(CommandHandler("help", help_command))
    dispatcher.add_handler(CommandHandler("update", update_data))

    updater.start_polling()
    updater.idle()


if __name__ == '__main__':
    main()
