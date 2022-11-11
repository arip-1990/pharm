import { FC, useEffect, useState } from "react";
import { useMounted } from "../../hooks/useMounted";
import api from '../../lib/api';

import styles from './Button.module.scss';

const Button: FC = () => {
    const [isAndroid, setIsAndroid] = useState<boolean>(false);
    const [marginTop, setMarginTop] = useState<number>(0);
    const [apkLink, setApkLink] = useState<string>();
    const isMounted = useMounted();

    useEffect(() => {
        const fetchLink = async () => {
            const {data} = await api.get<string>('/get-apk-link');
            console.log(data);
            setApkLink(data);
        }
        let timer: any = null;
        if (isMounted()) {
            setIsAndroid(navigator.userAgent.toLowerCase().indexOf('android') > -1);
            timer = setTimeout(() => setMarginTop(parseInt(document.documentElement.style.marginTop)), 500);
            fetchLink();
        }

        return () => clearTimeout(timer);
    }, []);

    return (
        <div className={styles.apkButton}  style={{marginTop: `${marginTop}px`}}>
            {apkLink && isAndroid && <a className='btn btn-primary' href={apkLink}>Скачать АПК файл</a>}
        </div>
    );
}

export default Button;
