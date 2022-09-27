import {FC} from "react";

import styles from "./TopInfo.module.scss";

interface Props {
	text?: string;
}

const DeliverySale: FC<Props> = ({text}) => {
	return (
		<div className={styles.deliverySale}>
			<span className={styles.deliverySale_text}>{text || 'При заказе от 2000 рублей доставка бесплатно'}</span>
		</div>
	);
}

export default DeliverySale;
