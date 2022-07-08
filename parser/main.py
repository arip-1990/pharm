from parser.Parser import Parser

if __name__ == '__main__':
    parser = Parser('./Товары без описания.xlsx')
    parser.start()
