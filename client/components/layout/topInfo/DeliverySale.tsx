import classNames from "classnames";
import {FC, useEffect, useState} from "react";
import { useMounted } from "../../../hooks/useMounted";

import styles from "./TopInfo.module.scss";

interface Props {
	text?: string;
}

const DeliverySale: FC<Props> = ({text}) => {
	const [show, setShow] = useState<boolean>(false);
	const isMounted = useMounted();

	useEffect(() => {
		let timer: any = null;
		if (isMounted()) timer = setTimeout(() => setShow(true), 1000);

		return () => clearTimeout(timer);
	}, [isMounted]);

	return (
		<div className={classNames(styles.deliverySale, {[styles.show]: show})}>
			<span className={styles.deliverySale_text}>{text || 'Бесплатная доставка при заказе от 2000 рублей'}</span>
		</div>
	);
}

export default DeliverySale;
