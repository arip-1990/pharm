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

        vendor = ''
        country = ''
        for item in self.soup.select('.body-wrap .content .detail .infos li'):
            param = item.select_one('.param')
            if param and 'завод-производитель' in param.text.strip().lower():
                param = item.select_one('.param-text')
                if param:
                    tmp = param.text.strip().split('(')
                    vendor = tmp[0].strip()
                    if len(tmp) > 1:
                        country = tmp[1].strip().strip(')')


        data['vendor'] = vendor
        data['country'] = country

        description = ''
        consist = ''
        for item in self.soup.select('.body-wrap .content .detail .product-information__info .product-information__info__content__block'):
            text = item.select_one('.product-information__info__content__block__title')
            if text:
                text = text.text.strip().lower()
                if 'описание' in text:
                    tmp = item.select('p')
                    if len(tmp):
                        description = '<br />'.join([i.text.strip() for i in tmp])
                elif 'состав' in text:
                    tmp = item.select('p')
                    if len(tmp):
                        consist = '<br />'.join([i.text.strip() for i in tmp])

        data['description'] = description
        data['consist'] = consist
        data['image'] = self.parse_image()

        return data

    def parse_image(self) -> str:
        data = self.soup.select_one('.body-wrap .content .pic-slider .pic-img img')
        return data.get('src').strip() if data else ''
