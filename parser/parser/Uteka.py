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
        data['description'] = self.parse_description()
        data['image'] = self.parse_image()

        return data

    def parse_description(self) -> str | None:
        data = None
        for item in self.soup.select('.product-page #instructions [itemprop=description] [data-test=instruction]'):
            tmp = item.select_one('h3')
            if tmp and tmp.text.strip().lower().startswith('описание'):
                description = '\n'.join([str(item) for item in item.select('.tinymce-content > *')])
                data = description.strip()

        return data

    def parse_image(self) -> str | None:
        data = self.soup.select_one('.product-page .image-slider picture img')
        return data.get('src').strip() if data else None
