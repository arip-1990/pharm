import re
import os
import requests
import pathlib
import unicodedata
import pandas as pd
from time import time
from fuzzywuzzy import fuzz
from bs4 import BeautifulSoup
from threading import Thread, Event
from selenium import webdriver
from selenium.common.exceptions import WebDriverException
from .Asna import Asna
from .Apteka import Apteka
from .EApteka import EApteka
from .Farmacy import Farmacy
from .Uteka import Uteka
from .MegApteka import MegApteka


class Parser:
    SEARCH_URL = 'https://yandex.ru/search/?text='
    AGENT = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/102.0.0.0 Safari/537.36'
    proxies = []
    code = None
    title = None
    max_progress_time = 0
    min_progress_time = 0
    message = ''
    total_found = 0

    def __init__(self, file: str, headless: bool = False, proxy: str = None):
        self.excel_data = pd.read_excel(file, 'Products')
        self.total = len(self.excel_data)
        self.progress = 1

        options = webdriver.ChromeOptions()
        if headless:
            options.add_argument('--headless')
        options.add_argument('--window-size=1280,720')
        options.add_argument('--disable-xss-auditor')
        options.add_argument('--disable-web-security')
        options.add_argument('--disable-dev-shm-usage')
        options.add_argument('--allow-running-insecure-content')
        options.add_argument('--no-sandbox')
        options.add_argument('--disable-setuid-sandbox')
        options.add_argument('--disable-webgl')
        options.add_argument('--disable-popup-blocking')
        options.add_argument(f'--user-agent={self.AGENT}')
        options.add_argument('--load-extension=./stopper')
        if proxy:
            options.add_argument(f'--proxy-server={proxy}')

        self.driver = webdriver.Remote(os.getenv('SELENIUM_URL'), desired_capabilities=options.to_capabilities())
        self.driver.implicitly_wait(10)
        self.driver.set_page_load_timeout(20)
        # self.status_event = Event()

    def close(self) -> None:
        self.driver.close()

    def parse_proxy(self) -> None:
        r = requests.get('https://free-proxy-list.net')
        if r.status_code == 200:
            soup = BeautifulSoup(r.text, 'html.parser')
            for item in soup.select('#list table tbody tr'):
                tmp = item.select('td')
                if tmp[4].text.strip() != 'transparent':
                    self.proxies.append(f'http://{tmp[0].text.strip()}:{tmp[1].text.strip()}')

    @staticmethod
    def remove_control_characters(s: str) -> str:
        return "".join(ch for ch in s if unicodedata.category(ch)[0] != "C")

    def remove_tags(self, text: str) -> str:
        text = unicodedata.normalize('NFKD', re.compile(r'<[^>]+>').sub('', text))
        return self.remove_control_characters(text)

    def find_product_by_name(self, url: str, result: list) -> None:
        self.driver.get(url)
        page = self.driver.execute_script('return document.body.innerHTML;')
        parser = None
        if 'eapteka.ru' in url:
            parser = EApteka(page)
        if 'megapteka.ru' in url:
            parser = MegApteka(page)
        # elif 'farmacy.ru' in url:
        #     parser = Farmacy(page)
        elif 'apteka.ru' in url:
            parser = Apteka(page)
        elif 'asna.ru' in url:
            parser = Asna(page)
        elif 'uteka.ru' in url:
            parser = Uteka(page)

        if parser:
            data = parser.parse()
            if data:
                data['url'] = url
                result.append(data)

    def find_product(self, urls: list) -> dict:
        data = {}
        product = {}
        link = ''
        search = []
        # threads = []
        # for url in urls:
        #     t = Thread(target=self.find_product_by_name, args=(url, search))
        #     threads.append(t)
        #     t.start()
        #
        # for thread in threads:
        #     thread.join()

        for url in urls:
            self.find_product_by_name(url, search)

        percent = 50
        for item in search:
            tmp = fuzz.token_set_ratio(self.title, item['title'])
            if percent < tmp:
                percent = tmp
                product = item
                link = item['url']

        if product:
            data = {
                'code': self.code,
                'title': self.title,
                'product': product,
                'url': link
            }

        return data

    def start(self) -> None:
        # Thread(target=self.print_status).start()
        for index, row in self.excel_data.iterrows():
            self.code = str(row['Код']).replace('.0', '')
            self.title = str(row['Наименование']).replace('.0', '')

            try:
                self.driver.get(self.SEARCH_URL + self.title)
                page = self.driver.execute_script('return document.body.innerHTML;')

                urls = []
                soup = BeautifulSoup(page, 'html.parser')
                for link in soup.select('#search-result .OrganicTitle a'):
                    tmp = link.get('href').strip()
                    if 'eapteka.ru' in tmp or 'apteka.ru' in tmp or 'megapteka.ru' in tmp or 'asna.ru' in tmp or 'uteka.ru' in tmp:
                        urls.append(tmp)

                product = self.find_product(urls)
                if product:
                    self.total_found += 1
                    self.save_to_excel(product)
            except WebDriverException as e:
                self.message = e.msg
            self.progress += 1

        # self.status_event.set()
        self.driver.quit()

    def save_to_excel(self, data: dict):
        temp = {
            'Код': [],
            'Наименование': [],
            'Описание': [],
            'Картинка': [],
            'Ссылка': []
        }
        temp['Код'].append(data['code'])
        temp['Наименование'].append(data['title'])
        temp['Описание'].append(data['product']['description'])
        temp['Картинка'].append(data['product']['image'])
        temp['Ссылка'].append(data['url'])
        df = pd.DataFrame(temp)
        file = pathlib.Path('./data/Найденные товары.xlsx')
        if file.is_file():
            df = pd.concat(
                [pd.DataFrame(pd.read_excel('./data/Найденные товары.xlsx', 'Products')), df], ignore_index=True)

        df.to_excel('./data/Найденные товары.xlsx', sheet_name='Products', index=False)

    @staticmethod
    def get_format_time(timestamp: float) -> str:
        h = int(timestamp // 3600)
        m = int(timestamp % 3600 // 60)
        s = int(timestamp % 3600 % 60)
        tmp = str(h) + 'ч ' if h else ''
        tmp += str(m) + 'м ' if m else ''
        tmp += str(s) + 'с'

        return tmp

    def print_status(self):
        loader = ['-', '\\', '|', '/']
        i = 0
        old_progress = self.progress
        current_time = time()
        while not self.status_event.wait(0.2):
            width, _ = os.get_terminal_size()
            total_time = int((self.max_progress_time + self.min_progress_time) / 2 * (self.total - self.progress))
            left = self.get_format_time(total_time) if 1 < self.progress else 'считаем'
            width_1 = width - (19 + len(str(self.title)) + len(str(self.total_found)))
            width_2 = width - (42 + len(str(self.progress)) + len(str(self.total)) + len(left))
            title = self.title
            if len(title) > (width - (19 + len(str(self.total_found)))):
                title = title[:(width - (19 + len(str(self.total_found))) - 3)] + '...'
            print(
                f'\033[1J\033[HИщем по: {title} \033[{width_1}C\033[1;32mНайдено: {self.total_found}\033[0m')
            print(
                f'\033[2K{loader[i]} Парсинг страниц: {self.progress} из {self.total} \033[{width_2}C\033[1;32mОсталось времени: {left}\033[0m')
            print('\n\033[2K\033[1;31mОшибки:\033[0m')
            print(f'\033[2K{self.message}', end='')

            i += 1
            if i == 4:
                i = 0
            if old_progress < self.progress:
                old_progress = self.progress
                tmp = time() - current_time
                current_time = time()
                if self.max_progress_time < tmp:
                    self.max_progress_time = tmp
                if not self.min_progress_time or self.min_progress_time > tmp:
                    self.min_progress_time = tmp
