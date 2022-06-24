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
        # data['description'] = self.parse_description()
        data['images'] = '|'.join(self.parse_images())

        return data

    # def parse_description(self) -> str:
    #     data = ''
    #     tmp = self.driver.find_elements(By.XPATH, "//*[@class='desc' and contains(., 'Описание')]/../p/text()")
    #     for item in tmp:
    #         data += item.text.strip() + '\n'
    #     return data

    def parse_images(self) -> list:
        data = [item.get('src').strip() for item in self.soup.select('.c-product-images img')]
        return data
