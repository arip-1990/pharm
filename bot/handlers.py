from aiogram import F, Router, types
from aiogram.filters import Command
from aiogram.types import Message, CallbackQuery
from aiogram import flags
from aiogram.fsm.context import FSMContext

import config
import utils
from states import Gen

import kb
import text

router = Router()


@router.message(Command("start"))
async def start_handler(msg: Message):
    await msg.answer(text.greet.format(user_name=msg.from_user.full_name, user_id=msg.from_user.id), reply_markup=kb.main_menu)


@router.message(F.text == "Меню")
@router.message(F.text == "Выйти в меню")
@router.message(F.text == "◀️ Выйти в меню")
async def menu(msg: Message):
    await msg.answer(text.menu, reply_markup=kb.main_menu)


@router.callback_query(F.data == "import")
async def import_prompt(clbck: CallbackQuery, state: FSMContext):
    print(clbck.data)
    await clbck.message.edit_text(text.import_data, reply_markup=kb.import_menu)


@router.callback_query(F.data == "search")
async def search_prompt(clbck: CallbackQuery, state: FSMContext):
    print(clbck.data)
    await clbck.message.edit_text(text.search_data, reply_markup=kb.search_menu)


@router.callback_query(F.data == "category")
@router.callback_query(F.data == "product")
@router.callback_query(F.data == "store")
@router.callback_query(F.data == "offer")
@flags.chat_action("typing")
async def import_data_handler(clbck: CallbackQuery, state: FSMContext):
    await state.set_state(Gen.import_prompt)

    match clbck.data:
        case "category": output_text = "Выгрузка категории на сайт (из 1c)"
        case "product": output_text = "Выгрузка товаров на сайт (из 1c)"
        case "store": output_text = "Выгрузка аптек на сайт (из 1c)"
        case "offer": output_text = "Выгрузка остатков на сайт (из 1c)"

    await data_handler('import', clbck, output_text)


@router.callback_query(F.data == "init")
@router.callback_query(F.data == "reindex")
@flags.chat_action("typing")
async def search_data_handler(clbck: CallbackQuery, state: FSMContext):
    print(clbck.data)
    await state.set_state(Gen.search_prompt)

    match clbck.data:
        case "init": output_text = "Инициализация индекса товаров для поиска"
        case "reindex": output_text = "Обновление индекса товаров для поиска"

    await data_handler('search', clbck, output_text)


async def data_handler(api_type: str, clbck: CallbackQuery, output_text: str):
    await clbck.message.edit_text(text.data_handler.format(output_text=output_text))
    await clbck.message.answer(text.text_wait, reply_markup=kb.exit_kb)

    if config.APP_ENV == 'prod':
        await clbck.bot.send_message(
            Gen.channel_notification,
            text.data_handler_from_user.format(user_name=clbck.message.from_user.full_name, output_text=output_text)
        )

    await utils.send_command_api(api_type, clbck.data)
