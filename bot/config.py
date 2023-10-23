from typing import Optional

from pydantic import SecretStr, RedisDsn
from pydantic_settings import BaseSettings, SettingsConfigDict


class Settings(BaseSettings):
    ADMIN_ID: int = 1195813156
    CHANNEL: int = -1001619975317
    APP_ENV: Optional[str] = 'prod'
    BOT_TOKEN: SecretStr
    REDIS_HOST: str = 'redis://localhost'

    model_config = SettingsConfigDict(env_file='.env', env_file_encoding='utf-8')


config = Settings()
