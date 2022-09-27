import {FC, MouseEvent, useState} from "react";
import {SetCity} from "./SetCity";
import {Container} from "react-bootstrap";
import Link from "next/link";
import Image from "next/image";
import {useAuth} from "../../../hooks/useAuth";
import Auth from "../../auth";

import deliverySale from '../../../assets/images/delivery-sale.png';

import styles from './TopInfo.module.scss';

const TopInfo: FC = () => {
	const { isAuth } = useAuth();
	const [showModal, setShowModal] = useState<boolean>(false);

	const handleSignIn = (e: MouseEvent) => {
		e.preventDefault();
		setShowModal(true);
	};

	return (
		<Container className='my-3'>
			<div className={styles.topInfo}>
				<SetCity />

				<div className={styles.deliverySale}>
					<Image src={deliverySale} />
				</div>

				<div className="auth text-end">
					<span className="phone">+7 (8722) 606-366</span>
					<span className="d-inline-block">
            {isAuth ? (
							<Link href="/pages/profile">
								<a>Личный кабинет</a>
							</Link>
						) : (
							<a href="#" onClick={handleSignIn}>
								Войти
							</a>
						)}
          </span>
				</div>

				<Auth show={showModal} onHide={() => setShowModal(false)} />
			</div>
		</Container>
	);
}

export default TopInfo;
