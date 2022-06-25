from bs4 import BeautifulSoup


class Apteka:
    def __init__(self, page: str):
        self.soup = BeautifulSoup(page, 'html.parser')

    def parse(self) -> dict | None:
        data = {}
        title = self.soup.select_one('.ViewProductPage .ViewProductPage__title')
        if title is None:
            return None

        data['title'] = title.text.strip()
        data['description'] = self.parse_description()
        data['image'] = self.parse_image()

        return data

    def parse_description(self) -> str | None:
        data = None
        for item in self.soup.select('.ViewProductPage .ProductBottomInfo .ProductDescription .ProdDescList'):
            tmp = item.select_one('h3')
            if tmp and tmp.text.strip().lower().startswith('описание'):
                description = '\n'.join([str(item) for item in self.soup.select('.ViewProductPage .ProductBottomInfo '
                                                                                '.ProductDescription .ProdDescList > '
                                                                                '*:not(h3)')])
                data = description.strip()

        return data

    def parse_image(self) -> str | None:
        data = self.soup.select_one('.ViewProductPage .ViewProductPage__photo img')
        return data.get('src').strip() if data else None
