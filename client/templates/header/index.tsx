import { ChangeEvent, FC, useEffect, useState } from "react";
import Image from "next/image";
import Link from "next/link";
import { useRouter } from "next/router";
import { useFormik } from "formik";
import { Container, Row, Col, Dropdown } from "react-bootstrap";
import { useLocalStorage } from "react-use-storage";

import Navbar from "./Navbar";
import Logo from "../../assets/images/logo.svg";
import heart from "../../assets/images/heart.png";
import cart from "../../assets/images/cart.png";
import { ICart } from "../../models/ICart";
import { IProduct } from "../../models/IProduct";
import { useSearchNameProductsQuery } from "../../lib/catalogService";

const Header: FC = () => {
  const router = useRouter();
  const [carts] = useLocalStorage<ICart[]>("cart", []);
  const [favorites] = useLocalStorage<IProduct[]>("favorites", []);
  const [searchText, setSearchText] = useState<string>();
  const [totalCart, setTotalCart] = useState<number>(0);
  const [totalFavorite, setTotalFavorite] = useState<number>(0);
  const { data, isFetching } = useSearchNameProductsQuery(searchText, { skip: !searchText || searchText.length < 3 });

  useEffect(() => {
    let total = 0;
    carts?.forEach((item) => (total += item.quantity));
    setTotalCart(total);
    setTotalFavorite(favorites.length);
  }, [carts, favorites]);

  const formik = useFormik({
    initialValues: { q: "" },
    onSubmit: ({ q }) => {
      if (q.length >= 3) router.push(`/catalog/search?q=${q}`);
    },
  });

  const handleChange = (e: ChangeEvent<HTMLInputElement>) => {
    setSearchText(e.currentTarget.value);
    formik.handleChange(e);
  }

  return (
    <Container as="header" className="my-3">
      <div className="empty-box" />
      <div className="fixed-box">
        <Row className="container align-items-center p-0 mx-auto">
          <Col xs={6} sm={6} md={4} lg={3} className="me-auto me-lg-0">
            <Link href="/">
              <a>
                <Logo className="logo" />
              </a>
            </Link>
          </Col>
          <Col
            xs={{ span: 12, order: 3 }}
            sm={{ span: 12, order: 3 }}
            lg={{ span: 7, order: 0 }}
            className="mt-3 mt-lg-0"
          >
            <form className="search" onSubmit={formik.handleSubmit}>
              <Dropdown>
                <input
                  type="search"
                  name="q"
                  className="form-control"
                  placeholder="Введите название препарата"
                  onChange={handleChange}
                  value={formik.values.q}
                />
                <Dropdown.Menu show={!!searchText && !isFetching && !!data?.length}>
                  {data?.map(item => (
                    <Dropdown.Item
                      key={item.id}
                      href={`/catalog/product/${item.slug}`}
                      dangerouslySetInnerHTML={{ __html: item.highlight.replace('<em>', '<em class="text-warning">') }}
                    />
                  ))}
                </Dropdown.Menu>
              </Dropdown>
              <button type="submit" className="btn btn-primary btn-search">
                Найти
              </button>
            </form>
          </Col>
          <Col
            xs={3}
            sm={2}
            lg={1}
            className="text-center"
            style={{ marginTop: 19 }}
          >
            <Link href="/favorite">
              <a className="fav">
                <span style={{ display: "inline-block", position: "relative" }}>
                  <span className="quantity">{totalFavorite}</span>
                  <Image src={heart} height={30} />
                </span>
                <br />
                <span>Избранное</span>
              </a>
            </Link>
          </Col>
          <Col
            xs={3}
            sm={2}
            lg={1}
            className="text-center"
            style={{ marginTop: 19 }}
          >
            <Link href="/cart">
              <a className="cart">
                <span style={{ display: "inline-block", position: "relative" }}>
                  <span className="quantity">{totalCart}</span>
                  <Image src={cart} height={30} />
                </span>
                <br />
                <span>Корзина</span>
              </a>
            </Link>
          </Col>
        </Row>
      </div>

      <Row>
        <Navbar />
      </Row>
    </Container>
  );
};

export default Header;
