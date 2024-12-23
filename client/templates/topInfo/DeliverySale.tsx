import classNames from "classnames";
import { FC, useEffect, useState } from "react";
import { useMounted } from "../../hooks/useMounted";

import styles from "./TopInfo.module.scss";

const DeliverySale: FC = () => {
  const [show, setShow] = useState<boolean>(false);
  const isMounted = useMounted();

  useEffect(() => {
    let timer: NodeJS.Timeout = null;
    if (isMounted()) timer = setTimeout(() => setShow(true), 1000);

    return () => clearTimeout(timer);
  }, []);

  return isMounted() ? (
    <div className={classNames(styles.deliverySale, { [styles.show]: show })}>
      <span className={styles.deliverySale_text}>
        Бесплатная доставка при заказе от 2000 рублей
      </span>
    </div>
  ) : null;
};

export default DeliverySale;
