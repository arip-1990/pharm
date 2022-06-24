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
        data['description'] = self.parse_description()
        data['image'] = self.parse_image()

        return data

    def parse_description(self) -> str | None:
        data = self.soup.select_one('.item-block .item-block-scr #description > *:not(h2)')
        if data is None:
            return None

        return data.text.strip()

    def parse_image(self) -> str | None:
        data = self.soup.select_one('app-html-image img')
        return data.get('src').strip() if data else None
