import { FC, MouseEvent, useCallback, useState } from "react";
import { Modal } from "react-bootstrap";

import Login from "./Login";
import Register from "./Register";
import ResetPassword from "./ResetPassword";

import styles from "./Auth.module.scss";

type AuthType = "login" | "register" | "resetPassword";

interface Props {
  show: boolean;
  onHide: () => void;
  type?: AuthType;
}

const Auth: FC<Props> = ({ show, onHide, type = "login" }) => {
  const [authType, setAuthType] = useState<AuthType>(type);

  const handleSwitchAuth = useCallback((e: MouseEvent) => {
    e.preventDefault();
    setAuthType((item) => (item === "login" ? "register" : "login"));
  }, []);

  const handleClose = useCallback(() => {
    onHide();
    setTimeout(
      () => setAuthType(type === "register" ? "register" : "login"),
      500
    );
  }, [type]);

  return (
    <Modal
      dialogClassName={styles.dialog}
      contentClassName={styles.auth}
      show={show}
      onHide={handleClose}
      centered
    >
      <Modal.Body style={{ padding: "3rem" }}>
        {authType === "register" ? (
          <Register switchAuthType={setAuthType} />
        ) : authType === "resetPassword" ? (
          <ResetPassword switchAuthType={setAuthType} />
        ) : (
          <Login switchAuthType={setAuthType} onHide={handleClose} />
        )}
      </Modal.Body>
      {["login", "register"].includes(authType) ? (
        <Modal.Footer className="justify-content-center">
          <a href="#" className="text-primary" onClick={handleSwitchAuth}>
            {authType === "login" ? "Зарегистрироваться" : "Войти"}
          </a>
        </Modal.Footer>
      ) : null}
    </Modal>
  );
};

export default Auth;
