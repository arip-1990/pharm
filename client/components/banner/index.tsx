import { FC } from "react";

import styles from "./Banner.module.scss";

const Banner: FC = ({children}) => {
    return <div className={styles.banner}>{children}</div>
}

export default Banner;
