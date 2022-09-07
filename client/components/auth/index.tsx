import { FC, MouseEvent, useCallback, useEffect, useState } from "react";
import { Modal } from "react-bootstrap";
import Login from "./Login";
import Register from "./Register";
import VerifyPhone from "./VerifyPhone";
import SetPassword from "./SetPassword";
import { useRouter } from "next/router";
import styles from "./Auth.module.scss";
import ResetPassword from "./resetPassword";

type AuthType =
  | "login"
  | "register"
  | "verifyPhone"
  | "setPassword"
  | "resetPassword";

interface Props {
  show: boolean;
  type?: AuthType;
  onHide: () => void;
}

const Auth: FC<Props> = ({ show, onHide, type = "login" }) => {
  const [modal, setModal] = useState<{ open: boolean; type: AuthType }>({
    open: show,
    type,
  });
  const router = useRouter();

  useEffect(() => {
    const path = router.asPath.split("#");
    if (path.length > 1 && ["login", "register"].includes(path[1])) {
      const hash = path[1] as AuthType;
      setModal({ open: true, type: hash });
    }
  }, []);

  const handleHide = useCallback(() => {
    setModal((item) => ({ ...item, open: false }));
    show && onHide();
  }, [show]);

  const handleSwitchAuth = useCallback((e: MouseEvent) => {
    e.preventDefault();
    setModal((item) => ({
      ...item,
      type: item.type === "login" ? "register" : "login",
    }));
  }, []);

  const handleLogin = (
    success: boolean,
    other: "verifyPhone" | "setPassword" = null
  ) => {
    if (other) setModal((item) => ({ ...item, type: other }));
    else if (success) onHide();
  };

  const handleRegister = (success: boolean) => {
    if (success) {
      setModal((item) => ({ ...item, type: "verifyPhone" }));
    } else {
      onHide();
    }
  };

  const handleVerifyPhone = (
    success: boolean,
    setPassword: boolean = false
  ) => {
    if (success) onHide();
    else if (setPassword)
      setModal((item) => ({ ...item, type: "setPassword" }));
  };

  const handleSetPassword = (success: boolean, setPassword?: boolean) => {
    if (setPassword) setModal((item) => ({ ...item, type: "setPassword" }));
    else if (success) onHide();
  };

  const handleReseetPassword = (
    success: boolean,
    verifyPhone: boolean = false
  ) => {
    if (verifyPhone) setModal((item) => ({ ...item, type: "verifyPhone" }));
    else if (success) setModal((item) => ({ ...item, type: "login" }));
  };

  const getAuthForm = () => {
    switch (modal.type) {
      case "register":
        return <Register onSubmit={handleRegister} />;
      case "verifyPhone":
        return <VerifyPhone onSubmit={handleVerifyPhone} />;
      case "setPassword":
        return <SetPassword onSubmit={handleSetPassword} />;
      case "resetPassword":
        return <ResetPassword onSubmit={handleReseetPassword} />;
      default:
        return (
          <Login
            onSubmit={handleLogin}
            onResetPassword={() =>
              setModal((item) => ({ ...item, type: "resetPassword" }))
            }
          />
        );
    }
  };

  return (
    <Modal
      dialogClassName={styles.dialog}
      contentClassName={styles.auth}
      show={modal.open || show}
      onHide={handleHide}
      centered
    >
      <Modal.Body style={{ padding: "3rem" }}>{getAuthForm()}</Modal.Body>
      {["login", "register"].includes(modal.type) ? (
        <Modal.Footer className="justify-content-center">
          <a href="#" className="text-primary" onClick={handleSwitchAuth}>
            {modal.type === "login" ? "Зарегистрироваться" : "Войти"}
          </a>
        </Modal.Footer>
      ) : null}
    </Modal>
  );
};

export default Auth;
