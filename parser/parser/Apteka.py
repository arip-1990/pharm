from bs4 import BeautifulSoup


class Apteka:
    def __init__(self, page: str):
        self.soup = BeautifulSoup(page, 'html.parser')

    def parse(self) -> dict | None:
        data = {}
        title = self.soup.select_one('.ViewProductPage .ViewProductPage__title')
        if title is None:
            return None

        data['title'] = title.text.strip()

        country = ''
        vendor = ''
        consist = ''
        description = ''
        for item in self.soup.select('.ViewProductPage .ProductBottomInfo .ProductDescription .ProdDescList'):
            title = item.select_one('h3')
            if title:
                text = title.text.strip().lower()
                if 'характеристики' in text:
                    for item2 in self.soup.select('dl div'):
                        text2 = item2.select_one('dt')
                        if text2 and 'страна' in text2.text.strip().lower():
                            text2 = item2.select_one('dd')
                            if text2:
                                country = text2.text.strip()
                        elif text2 and 'производитель' in text2.text.strip().lower():
                            text2 = item2.select_one('dd')
                            if text2:
                                vendor = text2.text.strip()
                elif 'состав' in text:
                    text2 = item.select_one('dl')
                    if text2:
                        consist = text2.text.strip()
                elif 'описание' in text:
                    text2 = item.select_one('dl')
                    if text2:
                        description = text2.text.strip()

        data['country'] = country
        data['vendor'] = vendor
        data['consist'] = consist
        data['description'] = description
        data['image'] = self.parse_image()

        return data

    def parse_image(self) -> str:
        data = self.soup.select_one('.ViewProductPage .ViewProductPage__photo img')
        return data.get('src').strip() if data else ''
