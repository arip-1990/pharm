import Image from "next/image";
import Link from "next/link";
import { Container, Row, Col, Modal } from "react-bootstrap";
import Navbar from "./Navbar";
import Logo from "../../../assets/images/logo.svg";
import heart from "../../../assets/images/heart.png";
import cart from "../../../assets/images/cart.png";
import { FC, MouseEvent, useEffect, useState } from "react";
import { Login, Register, CheckSms } from "../../auth";
import { SetCity } from "./SetCity";
import axios from "axios";
import { useAuth } from "../../../hooks/useAuth";
import api from "../../../lib/api";
import { ICart } from "../../../models/ICart";
import { useLocalStorage } from "react-use-storage";
import { IProduct } from "../../../models/IProduct";
import { useAlert } from "../../../hooks/useAlert";

const Header: FC = () => {
  const [loginType, setLoginType] = useState<"login" | "register" | "checkSms">(
    "login"
  );
  const [showModal, setShowModal] = useState<boolean>(false);
  const { isAuth, login } = useAuth();
  const [carts] = useLocalStorage<ICart[]>("cart", []);
  const [favorites] = useLocalStorage<IProduct[]>("favorites", []);
  const [totalCart, setTotalCart] = useState<number>(0);
  const [totalFavorite, setTotalFavorite] = useState<number>(0);
  const { addAlert } = useAlert();

  useEffect(() => {
    let total = 0;
    carts?.forEach((item) => (total += item.quantity));
    setTotalCart(total);
    setTotalFavorite(favorites.length);
  }, [carts, favorites]);

  const handleLogin = async (values: { login: string; password: string }) => {
    try {
      await login(values.login, values.password);
    } catch (error) {
      if (axios.isAxiosError(error)) console.log(error.response.data);
      else console.log(error);
    }

    setShowModal(false);
  };

  const handleRegister = async (values: {
    cardNum: string;
    lastName: string;
    firstName: string;
    middleName: string;
    email: string;
    phone: string;
    birthDate: string;
    gender: number;
    password: string;
    rule: number;
  }) => {
    try {
      await api.post("register", { ...values });
      setLoginType("checkSms");
    } catch (error) {
      if (axios.isAxiosError(error)) {
        console.log(error.response.data);
        addAlert("danger", String(error.response.data));
      } else console.log(error);
      setShowModal(false);
    }
  };

  const handleCheckSms = async (values: { smsCode: string }) => {
    try {
      await api.post("checkSms", { ...values });
      setShowModal(false);
    } catch (error) {
      if (axios.isAxiosError(error)) console.log(error.response.data);
      else console.log(error);
    }
  };

  const handleSignIn = (e: MouseEvent) => {
    e.preventDefault();
    setShowModal(true);
  };

  return (
    <Container as="header" className="my-3">
      <Row>
        <SetCity className="col-5" />

        <Col xs={7} sm={7} className="auth text-end">
          <span className="phone">+7 (8722) 606-366</span>
          <span className="d-inline-block">
            {isAuth ? (
              <Link href="/profile/order">
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
            <form className="search">
              <input
                type="search"
                name="q"
                className="form-control"
                placeholder="Введите: название препарата, производителя, действующее вещество"
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
            <a className="fav" href="/favorite">
              <span style={{ display: "inline-block", position: "relative" }}>
                <span className="quantity">{totalFavorite}</span>
                <Image src={heart} height={30} />
              </span>
              <br />
              <span>Избранное</span>
            </a>
          </Col>
          <Col
            xs={3}
            sm={2}
            lg={1}
            className="text-center"
            style={{ marginTop: 19 }}
          >
            <a className="cart" href="/cart">
              <span style={{ display: "inline-block", position: "relative" }}>
                <span className="quantity">{totalCart}</span>
                <Image src={cart} height={30} />
              </span>
              <br />
              <span>Корзина</span>
            </a>
          </Col>
        </Row>
      </div>

      <Row>
        <Navbar />
      </Row>

      <Modal
        size="sm"
        show={showModal}
        onHide={() => setShowModal(false)}
        aria-labelledby="contained-modal-title-vcenter"
        centered
      >
        {loginType !== "checkSms" ? (
          <Modal.Header closeButton>
            <Modal.Title id="contained-modal-title-vcenter">
              <h5 className="modal-title">
                {loginType === "login" ? "Войти" : "Регистрация"}
              </h5>
            </Modal.Title>
          </Modal.Header>
        ) : null}
        <Modal.Body>
          {loginType === "login" ? (
            <Login onSubmit={handleLogin} />
          ) : loginType === "register" ? (
            <Register onSubmit={handleRegister} />
          ) : (
            <CheckSms onSubmit={handleCheckSms} />
          )}
        </Modal.Body>
        {loginType !== "checkSms" ? (
          <Modal.Footer className="justify-content-center">
            <a
              href="#"
              className="text-primary"
              onClick={() =>
                setLoginType(loginType === "login" ? "register" : "login")
              }
            >
              {loginType === "login" ? "Зарегистрироваться" : "Войти"}
            </a>
          </Modal.Footer>
        ) : null}
      </Modal>
    </Container>
  );
};

export default Header;
