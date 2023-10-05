from aiogram.types import InlineKeyboardButton, InlineKeyboardMarkup, KeyboardButton, ReplyKeyboardMarkup, ReplyKeyboardRemove


main_menu = [
    [InlineKeyboardButton(text="Выгрузка данных на сайт", callback_data="import"),
     InlineKeyboardButton(text="Обновление индексов поиска", callback_data="search")],
]

import_menu = [
    [InlineKeyboardButton(text="Выгрузка категории", callback_data="category"),
    InlineKeyboardButton(text="Выгрузка товаров", callback_data="product")],
    [InlineKeyboardButton(text="Выгрузка аптек", callback_data="store"),
    InlineKeyboardButton(text="Выгрузка остатков", callback_data="offer")],
]

search_menu = [
    [InlineKeyboardButton(text="Инициализация индексов поиска", callback_data="init"),
    InlineKeyboardButton(text="Обновление индексов поиска", callback_data="reindex")],
]

main_menu = InlineKeyboardMarkup(inline_keyboard=main_menu)
import_menu = InlineKeyboardMarkup(inline_keyboard=import_menu)
search_menu = InlineKeyboardMarkup(inline_keyboard=search_menu)


exit_kb = ReplyKeyboardMarkup(keyboard=[[KeyboardButton(text="◀️ Выйти в меню")]], resize_keyboard=True)
iexit_kb = InlineKeyboardMarkup(inline_keyboard=[[InlineKeyboardButton(text="◀️ Выйти в меню", callback_data="menu")]])

# builder = InlineKeyboardBuilder()
# for i in range(15):
#     builder.button(text=f”Кнопка {i}”, callback_data=f”button_{i}”)
# builder.adjust(2)
# await msg.answer(“Текст сообщения”, reply_markup=builder.as_markup())
