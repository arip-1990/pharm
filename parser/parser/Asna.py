from bs4 import BeautifulSoup


class Asna:
    def __init__(self, page: str):
        self.soup = BeautifulSoup(page, 'html.parser')

    def parse(self) -> dict | None:
        data = {}
        title = self.soup.select_one('.body-wrap .content .product-title')
        if title is None:
            return None

        data['title'] = title.text.strip()
        data['description'] = self.parse_description()
        data['image'] = self.parse_image()

        return data

    def parse_description(self) -> str | None:
        data = None
        for item in self.soup.select('.content .detail .product-information .product-information__info__content__block'):
            tmp = item.select_one('h3')
            if tmp and tmp.text.strip().lower().startswith('описание'):
                description = '\n'.join([str(item) for item in self.soup.select('.content .detail '
                                                                                '.product-information '
                                                                                '.product'
                                                                                '-information__info__content__block > '
                                                                                '*:not(h3)')])
                data = description.strip()

        return data

    def parse_image(self) -> str | None:
        data = self.soup.select_one('.body-wrap .content .pic-slider .pic-slider-wrap img')
        return data.get('src').strip() if data else None
