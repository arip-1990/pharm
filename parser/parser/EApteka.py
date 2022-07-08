from bs4 import BeautifulSoup


class EApteka:
    def __init__(self, page: str):
        self.soup = BeautifulSoup(page, 'html.parser')

    def parse(self) -> dict | None:
        data = {}
        title = self.soup.select_one('.sec-item > h1')
        if title is None:
            return None

        data['title'] = title.text.strip()

        vendor = self.soup.select_one('#binstructions [itemprop=description] #instruction_MANUFACTURER .offer-instruction__item-text')
        if vendor:
            tmp = vendor.text.strip().split(',')
            data['vendor'] = tmp[0].strip()
            if len(tmp) > 1:
                data['country'] = tmp[1].strip()
            else:
                data['country'] = ''
        else:
            data['vendor'] = ''
            data['country'] = ''

        description = self.soup.select_one('#binstructions [itemprop=description] #instruction_DESCRIPTION .offer-instruction__item-text')
        data['description'] = description.text.strip() if description else ''

        consist = self.soup.select_one('#binstructions [itemprop=description] #instruction_COMPOSITION .offer-instruction__item-text')
        data['consist'] = consist.text.strip() if consist else ''

        data['image'] = self.parse_image()

        return data

    def parse_image(self) -> str:
        data = self.soup.select_one('.sec-item .gallery .slick-current img')
        return data.get('src').strip() if data else ''
