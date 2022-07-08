from bs4 import BeautifulSoup


class MegApteka:
    def __init__(self, page: str):
        self.soup = BeautifulSoup(page, 'html.parser')

    def parse(self) -> dict | None:
        data = {}
        title = self.soup.select_one('.item-block .item-block-first .item-block-first-title > h1')
        if title is None:
            return None

        data['title'] = title.text.strip()

        vendor = ''
        country = ''
        for item in self.soup.select('.item-block .item-block-first .item-block-first-centre .c-props-chosen > div'):
            param = item.select_one('.props-chosen__name')
            if param and 'страна' in param.text.strip().lower():
                text = item.select_one('.props-chosen__stat')
                if text:
                    country = text.text.strip()
            elif param and 'производитель' in param.text.strip().lower():
                text = item.select_one('.c-value__value-text')
                if text:
                    vendor = text.text.strip()

        data['vendor'] = vendor
        data['country'] = country

        description = self.soup.select_one('.item-block .item-block-scr #description > *:not(h2)')
        data['description'] = description.text.strip() if description else ''

        consist = self.soup.select_one('.item-block .item-block-scr #COMPOSITION > *:not(h2)')
        data['consist'] = consist.text.strip() if consist else ''

        data['image'] = self.parse_image()

        return data

    def parse_image(self) -> str:
        data = self.soup.select_one('app-html-image img')
        return data.get('src').strip() if data else ''
