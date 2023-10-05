import os


APP_ENV = os.getenv('APP_ENV', 'prod')
BOT_TOKEN = os.getenv('BOT_TOKEN')
REDIS_HOST = os.getenv('REDIS_HOST', 'localhost')