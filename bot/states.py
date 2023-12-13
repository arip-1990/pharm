from aiogram.filters.state import StatesGroup, State


class Main(StatesGroup):
    START = State()
    UPDATE_DATA = State()
    UPDATE_SEARCH = State()
