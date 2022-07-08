from bs4 import BeautifulSoup


class Uteka:
    def __init__(self, page: str):
        self.soup = BeautifulSoup(page, 'html.parser')

    def parse(self) -> dict | None:
        data = {}
        title = self.soup.select_one('.product-page #card [itemprop=name]')
        if title is None:
            return None

        data['title'] = title.text.strip()

        vendor = self.soup.select_one('.product-page #card [itemprop=brand] [itemprop=name]')
        data['vendor'] = vendor.text.strip() if vendor else ''

        country = self.soup.select_one('.product-page #card [data-test=country]')
        data['country'] = country.text.strip() if country else ''

        description = ''
        consist = ''
        for item in self.soup.select('.product-page #instructions [itemprop=description] [data-test=instruction]'):
            tmp = item.select_one('h3')
            if tmp and 'описание' in tmp.text.strip().lower():
                text = tmp.next_sibling
                if text:
                    description = text.text.strip()
            elif tmp and 'состав' in tmp.text.strip().lower():
                text = tmp.next_sibling
                if text:
                    consist = text.text.strip()

        data['description'] = description
        data['consist'] = consist

        data['image'] = self.parse_image()

        return data

    def parse_image(self) -> str:
        data = self.soup.select_one('.product-page .image-slider picture img')
        return data.get('src').strip() if data else ''
