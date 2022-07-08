from bs4 import BeautifulSoup


class Wildberries:
    def __init__(self, page: str):
        self.soup = BeautifulSoup(page, 'html.parser')

    def parse(self) -> dict | None:
        data = {}
        title = self.soup.select_one('#app .product-page__header [data-link="text{:product^goodsName}"]')
        if title is None:
            return None

        data['title'] = title.text.strip()

        vendor = self.soup.select_one('#app .product-page__header [data-link="text{:product^brandName}"]')
        data['vendor'] = vendor.text.strip() if vendor else ''

        country = ''
        for item in self.soup.select('#app .product-params .product-params__table .product-params__row'):
            columns = item.select('.product-params__cell')
            if len(columns) > 1 and 'страна' in columns[0].text.strip().lower():
                country = columns[1].text.strip()
                break

        data['country'] = country

        description = self.soup.select_one('#app .product-page .details-section .details-section__details [data-link="text{:product^description}"]')
        data['description'] = description.text.strip() if description else ''

        consist = self.soup.select_one('#app .product-page .details-section .details-section__details [data-link="text{:product^consist}"]')
        data['consist'] = consist.text.strip() if consist else ''

        data['image'] = self.parse_image()

        return data

    def parse_image(self) -> str:
        data = self.soup.select_one('#app #photo #imageContainer img')
        return data.get('src').strip() if data else ''
