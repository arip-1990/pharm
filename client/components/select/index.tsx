import classNames from "classnames";
import { FC, MouseEvent, useState } from "react";

import styles from "./Modal.module.scss";

type Props = {
  value: string;
  options: string[];
};

const Select: FC<Props> = ({ value, options }) => {
  const [show, setShow] = useState<boolean>(false);

  const handleClose = (e: MouseEvent<HTMLDivElement>) => {
    e.stopPropagation();
    setShow(false);
  };

  return (
    <div className={styles.container}>
      <div
        className={classNames(styles.email, { [styles.expand]: show })}
        onClick={() => setShow(true)}
      >
        <div className={styles.from}>
          <div className={styles["from-contents"]}>
            <div className={styles.avatar + " " + styles.me} />
            <div className={styles.name}>{value}</div>
          </div>
        </div>
        <div className={styles.to}>
          <div className={styles["to-contents"]}>
            <div className={styles.top}>
              <div className={styles["avatar-large"] + " " + styles.me} />
              <div className={styles["name-large"]}>{value}</div>
              <div className={styles["x-touch"]} onClick={handleClose}>
                <div className={styles.x}>
                  <div className={styles.line1} />
                  <div className={styles.line2} />
                </div>
              </div>
            </div>
            <div className={styles.bottom}>
              {options.map((item) => (
                <div className={styles.row}>
                  <div className={styles.link}>
                    <a href="#">{item}</a>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Select;
