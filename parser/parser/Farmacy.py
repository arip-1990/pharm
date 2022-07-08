from bs4 import BeautifulSoup


class Farmacy:
    def __init__(self, page: str):
        self.soup = BeautifulSoup(page, 'html.parser')

    def parse(self) -> dict | None:
        data = {}
        title = self.soup.select_one('.c-header_h1')
        if title is None:
            return None

        data['title'] = title.text.strip()

        vendor = ''
        country = ''
        for item in self.soup.select('.c-block__content .c-product-features-overview .c-product-features-overview__item'):
            param = item.select_one('.c-value__label-text')
            if param and 'производитель' in param.text.strip().lower():
                text = item.select_one('.c-value__value-text')
                if text:
                    vendor = text.text.strip()

        data['vendor'] = vendor
        data['country'] = country

        description = ''
        consist = ''
        for item in self.soup.select('.c-product-page .c-product-page__content .c-block__content .desc'):
            if 'состав' in item.text.strip().lower():
                text = item.next_sibling
                if text:
                    consist = text.text.strip()
            elif 'описание' in item.text.strip().lower():
                text = item.select_one('.c-value__value-text')
                if text:
                    description = text.text.strip()

        data['description'] = description
        data['consist'] = consist
        data['image'] = self.parse_image()

        return data

    def parse_image(self) -> str:
        data = self.soup.select_one('.c-product-images img')
        return data.get('src').strip() if data else ''
