import { FC } from "react";
import Link from "next/link";
import { Nav, Navbar as BaseNavbar } from "react-bootstrap";
import classNames from "classnames";
import { useRouter } from "next/router";

const Navbar: FC = () => {
  const router = useRouter();

  return (
    <BaseNavbar expand="lg" className="navbar-primary">
      <BaseNavbar.Toggle aria-controls="navbarCollapse">
        <i className="icon-menu" />
      </BaseNavbar.Toggle>

      <BaseNavbar.Collapse id="navbarCollapse">
        <Nav className="m-md-0 align-items-center">
          <Nav.Item
            className="text-md-center mx-2"
            style={{ whiteSpace: "nowrap", overflowX: "hidden" }}
          >
            <Link href="/catalog">
              <a
                className={classNames("nav-link", {
                  active: router.pathname.startsWith("/catalog"),
                })}
                tabIndex={0}
              >
                <i className="icon-menu" /> Наш ассортимент
              </a>
            </Link>
          </Nav.Item>
          <Nav.Item
            className="text-md-center mx-2"
            style={{ whiteSpace: "nowrap", overflowX: "hidden" }}
          >
            <Link href="/store">
              <a
                className={classNames("nav-link", {
                  active: router.pathname.startsWith("/store"),
                })}
                tabIndex={0}
              >
                <i className="icon-hospital" /> Аптеки
              </a>
            </Link>
          </Nav.Item>
          {/* <Nav.Item className="text-md-center mx-2" style={{whiteSpace: 'nowrap', overflowX: 'hidden'}}>
            <Link href="/delivery-booking">
              <a
                className={classNames("nav-link", {
                  active: router.pathname.startsWith("/delivery-booking"),
                })}
                tabIndex={0}
              >
                <i className="icon-truck" /> Доставка/бронирование
              </a>
            </Link>
          </Nav.Item> */}
          <Nav.Item
            className="text-md-center mx-2"
            style={{ whiteSpace: "nowrap", overflowX: "hidden" }}
          >
            <Link href="/loyalty">
              <a
                className={classNames("nav-link", {
                  active:
                    router.pathname.startsWith("/loyalty") &&
                    !router.pathname.startsWith("/loyalty-rule"),
                })}
                tabIndex={0}
              >
                <i className="icon-loyalty" /> Программа лояльности
              </a>
            </Link>
          </Nav.Item>
          <Nav.Item
            className="text-md-center mx-2"
            style={{ whiteSpace: "nowrap", overflowX: "hidden" }}
          >
            <Link href="/stock">
              <a
                className={classNames("nav-link", {
                  active: router.pathname.startsWith("/stock"),
                })}
                tabIndex={0}
              >
                <i className="icon-gift" /> Акции
              </a>
            </Link>
          </Nav.Item>

          <Nav.Item
              className="text-md-center mx-2"
              style={{ whiteSpace: "nowrap", overflowX: "hidden" }}
          >
            <Link href="/kids">
              <a
                  className={classNames("nav-link", {
                    active: router.pathname.startsWith("/kids"),
                  })}
                  tabIndex={0}
              >
                <i className="icon-gift" /> Конкурс детского рисунка
              </a>
            </Link>
          </Nav.Item>

        </Nav>
      </BaseNavbar.Collapse>
    </BaseNavbar>
  );
};

export default Navbar;
