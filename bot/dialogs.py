import logging

from aiogram.types import CallbackQuery
from aiogram_dialog import Dialog, Window, DialogManager
from aiogram_dialog.widgets.kbd import Group, Button, SwitchTo
from aiogram_dialog.widgets.text import Const, Format

from states import Main


async def main_handler(callback: CallbackQuery, button: Button, manager: DialogManager):
    switched_window = Main.UPDATE_DATA
    if button.widget_id == 'search':
        switched_window = Main.UPDATE_SEARCH

    await manager.switch_to(switched_window)

async def update_handler(callback: CallbackQuery, button: Button, manager: DialogManager):
    pass

async def search_handler(callback: CallbackQuery, button: Button, manager: DialogManager):
    pass


main_window = Window(
    Format("Привет, {dialog_data[name]}!\nВаш ID: {dialog_data[id]}"),
    Group(
        Button(Const("Обновление данных"), "update", main_handler),
        Button(Const("Обновление индексов"), "search", main_handler),
        width=2
    ),
    state=Main.START,
)

update_window = Window(
    Const("Обновление данных на сервере!"),
    Group(
        Button(Const("Категории"), "category", update_handler),
        Button(Const("Товары"), "product", update_handler),
        Button(Const("Аптеки"), "store", update_handler),
        Button(Const("Остатки"), "offer", update_handler),
        width=2
    ),
    SwitchTo(Const("Назад"), "switch_to_main", Main.START),
    state=Main.UPDATE_DATA
)

search_window = Window(
    Const("Обновление индексов поиска на сервере!"),
    Group(
        Button(Const("Инициализация индексов"), "search_init", search_handler),
        Button(Const("Обновление индексов"), "search_update", search_handler),
        width=2
    ),
    SwitchTo(Const("Назад"), "switch_to_main", Main.START),
    state=Main.UPDATE_SEARCH
)


async def get_main_data(dialog_manager: DialogManager, **kwargs):
    return {
        'id': dialog_manager.event.from_user.id,
        'name': dialog_manager.event.from_user.full_name,
    }


dialog = Dialog(main_window, update_window, search_window, getter=get_main_data)
