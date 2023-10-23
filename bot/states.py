from aiogram_dialog import Dialog, Window
from aiogram_dialog.widgets.kbd import Button, Group
from aiogram_dialog.widgets.text import Const, Format
from aiogram.fsm.state import StatesGroup, State

class MyState(StatesGroup):
    main = State()
    import_prompt = State()
    search_prompt = State()


group = Group(
    Button(Const("Выгрузка данных на сайт"), id="import"),
    Button(Const("Обновление индексов поиска"), id="search"),
    width=2
)


dialog = Dialog(
    Window(
        Format("Привет, {event.from_user.full_name}!"),
        Format("Ваш ID: {event.from_user.id}"),
        group,
        state=MyState.main
    ),
)
