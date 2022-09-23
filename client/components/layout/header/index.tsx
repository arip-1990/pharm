import Image from "next/image";
import Link from "next/link";
import { Container, Row, Col } from "react-bootstrap";
import Navbar from "./Navbar";
import Logo from "../../../assets/images/logo.svg";
import heart from "../../../assets/images/heart.png";
import cart from "../../../assets/images/cart.png";
import { FC, MouseEvent, useEffect, useState } from "react";
import { SetCity } from "./SetCity";
import { useAuth } from "../../../hooks/useAuth";
import { ICart } from "../../../models/ICart";
import { useLocalStorage } from "react-use-storage";
import { IProduct } from "../../../models/IProduct";
import Auth from "../../auth";
import { useFormik } from "formik";
import { useRouter } from "next/router";

const Header: FC = () => {
  const [showModal, setShowModal] = useState<boolean>(false);
  const { isAuth } = useAuth();
  const router = useRouter();
  const [carts] = useLocalStorage<ICart[]>("cart", []);
  const [favorites] = useLocalStorage<IProduct[]>("favorites", []);
  const [totalCart, setTotalCart] = useState<number>(0);
  const [totalFavorite, setTotalFavorite] = useState<number>(0);

  useEffect(() => {
    let total = 0;
    carts?.forEach((item) => (total += item.quantity));
    setTotalCart(total);
    setTotalFavorite(favorites.length);
  }, [carts, favorites]);

  const handleSignIn = (e: MouseEvent) => {
    e.preventDefault();
    setShowModal(true);
  };

  const formik = useFormik({
    initialValues: { q: "" },
    onSubmit: ({ q }) => {
      if (q.length >= 3) router.push(`/catalog/search?q=${q}`);
    },
  });

  return (
    <Container as="header" className="my-3">
      <Row>
        <SetCity className="col-5" />

        <Col xs={7} sm={7} className="auth text-end">
          <span className="phone">+7 (8722) 606-366</span>
          <span className="d-inline-block">
            {isAuth ? (
              <Link href="/profile">
                <a>Личный кабинет</a>
              </Link>
            ) : (
              <a href="#" onClick={handleSignIn}>
                Войти
              </a>
            )}
          </span>
        </Col>
      </Row>

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
              <input
                type="search"
                name="q"
                className="form-control"
                placeholder="Введите: название препарата, производителя, действующее вещество"
                onChange={formik.handleChange}
                value={formik.values.q}
              />
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

      <Auth show={showModal} onHide={() => setShowModal(false)} />
    </Container>
  );
};

export default Header;
