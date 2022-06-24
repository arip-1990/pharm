from parser.Parser import Parser

if __name__ == '__main__':
    parser = Parser('./data/Товары без описания.xlsx', True)
    parser.start()
