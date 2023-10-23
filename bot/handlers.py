import config
import text
import utils


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
