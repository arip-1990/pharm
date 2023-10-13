from aiogram.fsm.state import StatesGroup, State

class Gen(StatesGroup):
    import_prompt = State()
    search_prompt = State()
