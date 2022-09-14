import { FC } from "react";
import Link from "next/link";
import { Nav, Navbar as BaseNavbar } from "react-bootstrap";
import classNames from "classnames";
import { useRouter } from "next/router";

const Navbar: FC = () => {
  const router = useRouter();

  return (
    <BaseNavbar expand="md" className="navbar-primary">
      <BaseNavbar.Toggle aria-controls="navbarCollapse">
        <i className="icon-menu" />
      </BaseNavbar.Toggle>

      <BaseNavbar.Collapse id="navbarCollapse">
        <Nav className="m-md-0 ">
          <Nav.Item className="text-md-center">
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
          <Nav.Item className="text-md-center">
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
          <Nav.Item className="text-md-center">
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
          </Nav.Item>
          <Nav.Item className="text-md-center">
            <Link href="/loyalty">
              <a
                className={classNames("nav-link", {
                  active: router.pathname.startsWith("/loyalty"),
                })}
                tabIndex={0}
              >
                <i className="icon-loyalty" /> Программа лояльности
              </a>
            </Link>
          </Nav.Item>
        </Nav>
      </BaseNavbar.Collapse>
    </BaseNavbar>
  );
};

export default Navbar;
