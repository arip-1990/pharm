import { Nav, Navbar } from "react-bootstrap";

export default () => {
    return (
        <Navbar expand='md' className="navbar-primary">
            <Navbar.Toggle aria-controls="navbarCollapse" />

            <Navbar.Collapse id="navbarCollapse">
                <Nav className="my-lg-0">
                    <Nav.Item className="sale">
                        <Nav.Link className="active">
                            <i className="fas fa-bars"></i> Наш ассортимент
                        </Nav.Link>
                    </Nav.Item>
                    <Nav.Item>
                        <Nav.Link href="{{ route('pharmacy') }}">
                            <i className="far fa-hospital"></i> Аптеки
                        </Nav.Link>
                    </Nav.Item>
                    <Nav.Item>
                        <Nav.Link href="{{ route('deliveryBooking') }}">
                            <i className="fas fa-ambulance"></i> Доставка/бронирование
                        </Nav.Link>
                    </Nav.Item>
                </Nav>
            </Navbar.Collapse>
        </Navbar>
    );
}
