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
        data['description'] = self.parse_description()
        data['image'] = self.parse_image()

        return data

    def parse_description(self) -> str | None:
        data = self.soup.select_one('#binstructions [itemprop=description] #instruction_DESCRIPTION .offer-instruction__item-text')
        if data is None:
            return None

        return data.text.strip()

    def parse_image(self) -> str | None:
        data = self.soup.select_one('.sec-item .gallery .slick-current img')
        return data.get('src').strip() if data else None
