import classNames from "classnames";
import { FC, MouseEvent, useCallback, useEffect, useState } from "react";
import styles from "./Loyalty.module.scss";

interface Props {
  onClick: () => void;
}

const Banner: FC<Props> = ({ onClick }) => {
  const [active, setActive] = useState<boolean>(false);

  useEffect(() => {
    addEventListener("mouseup", handleUp);

    return () => removeEventListener("mouseup", handleUp);
  }, []);

  const handleDown = useCallback((e: MouseEvent<HTMLButtonElement>) => {
    if (e.button === 0) setActive(true);
  }, []);

  const handleUp = useCallback(() => {
    setActive(false);
  }, []);

  const handleClick = useCallback(
    (e: MouseEvent<HTMLButtonElement>) => {
      e.preventDefault();
      onClick();
    },
    [onClick]
  );

  return (
    <div className={styles.banner}>
      <button
        className={classNames(styles.button, { [styles.active]: active })}
        onMouseDown={handleDown}
        onClick={handleClick}
      />
    </div>
  );
};

export default Banner;
