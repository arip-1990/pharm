import {FC, MouseEvent, useState} from "react";
import {SetCity} from "./SetCity";
import {Col, Container, Row} from "react-bootstrap";
import Link from "next/link";
import {useAuth} from "../../../hooks/useAuth";
import Auth from "../../auth";

const TopInfo: FC = () => {
	const { isAuth } = useAuth();
	const [showModal, setShowModal] = useState<boolean>(false);

	const handleSignIn = (e: MouseEvent) => {
		e.preventDefault();
		setShowModal(true);
	};

	return (
		<Container className='my-3'>
			<Row>
				<SetCity className="col-5" />

				<Col xs={7} sm={7} className="auth text-end">
					<span className="phone">+7 (8722) 606-366</span>
					<span className="d-inline-block">
            {isAuth ? (
							<Link href="/pages/profile">
								<a>Личный кабинет</a>
							</Link>
						) : (
							<a href="components/layout/topInfo/TopInfo#index.tsx" onClick={handleSignIn}>
								Войти
							</a>
						)}
          </span>
				</Col>

				<Auth show={showModal} onHide={() => setShowModal(false)} />
			</Row>
		</Container>
	);
}

export default TopInfo;
