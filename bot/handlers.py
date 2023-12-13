import logging

from aiogram import Router, F
from aiogram.types import Message, KeyboardButton, ReplyKeyboardMarkup, ReplyKeyboardRemove
from aiogram.filters import Command, CommandStart
from aiogram.fsm.context import FSMContext

from aiogram_dialog import DialogManager, StartMode

from states import Main, Update


handlers_router = Router()


@handlers_router.message(CommandStart())
async def start(message: Message, dialog_manager: DialogManager):
    await dialog_manager.start(Main.START, mode=StartMode.RESET_STACK)




@handlers_router.message(Command("cancel"))
@handlers_router.message(F.text.casefold() == "cancel")
async def cancel_handler(message: Message, state: FSMContext):
    current_state = await state.get_state()
    if current_state is None:
        return

    logging.info("Cancelling state %r", current_state)

    await state.clear()
    await message.answer("Cancelled.", reply_markup=ReplyKeyboardRemove())


@handlers_router.message(F.text.casefold() == "обновление данных")
async def update_handler(message: Message, dialog_manager: DialogManager):
    logging.info(message.text)
    logging.info(message.text.casefold())
    await dialog_manager.start(Update.DATA)


# @handlers_router.message(F.text.casefold() == "обновление индекса")
async def search_handler(message: Message, dialog_manager: DialogManager):
    logging.info(message.text)
    logging.info(message.text.casefold())
    # await dialog_manager.start(Update.SEARCH, mode=StartMode.RESET_STACK)


# @router.callback_query(F.data == "import")
# async def import_prompt(clbck: CallbackQuery, state: FSMContext):
#     await clbck.message.edit_text(text.import_data, reply_markup=kb.import_menu)
#
#
# @router.callback_query(F.data == "search")
# async def search_prompt(clbck: CallbackQuery, state: FSMContext):
#     await clbck.message.edit_text(text.search_data, reply_markup=kb.search_menu)
#
#
# @router.callback_query(F.data == "category")
# @router.callback_query(F.data == "product")
# @router.callback_query(F.data == "store")
# @router.callback_query(F.data == "offer")
# @flags.chat_action("typing")
# async def import_data_handler(clbck: CallbackQuery, state: FSMContext):
#     match clbck.data:
#         case "category": output_text = "Выгрузка категории на сайт (из 1c)"
#         case "product": output_text = "Выгрузка товаров на сайт (из 1c)"
#         case "store": output_text = "Выгрузка аптек на сайт (из 1c)"
#         case "offer": output_text = "Выгрузка остатков на сайт (из 1c)"
#
#
# @router.callback_query(F.data == "init")
# @router.callback_query(F.data == "reindex")
# @flags.chat_action("typing")
# async def search_data_handler(clbck: CallbackQuery, state: FSMContext):
#     match clbck.data:
#         case "init": output_text = "Инициализация индекса товаров для поиска"
#         case "reindex": output_text = "Обновление индекса товаров для поиска"
