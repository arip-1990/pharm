import { FC, useEffect, useState } from "react";
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
  const [loginType, setLoginType] = useState<AuthType>(type);
  const [openModal, setOpenModal] = useState<boolean>(show);
  const router = useRouter();

  useEffect(() => {
    const path = router.asPath.split("#");
    if (path.length > 1 && ["login", "register"].includes(path[1])) {
      const hash = path[1] as AuthType;
      setLoginType(hash);
      setOpenModal(true);
    }
  }, []);

  const handleHide = () => {
    setLoginType("login");
    onHide();
  };

  const handleLogin = (
    success: boolean,
    other: "verifyPhone" | "setPassword" = null
  ) => {
    if (other) setLoginType(other);
    else if (success) onHide();
  };

  const handleRegister = (success: boolean) => {
    if (success) {
      setLoginType("verifyPhone");
    } else {
      onHide();
    }
  };

  const handleVerifyPhone = (
    success: boolean,
    setPassword: boolean = false
  ) => {
    if (success) onHide();
    else if (setPassword) setLoginType("setPassword");
  };

  const handleSetPassword = (success: boolean, setPassword?: boolean) => {
    if (setPassword) setLoginType("setPassword");
    else if (success) onHide();
  };

  const handleReseetPassword = (
    success: boolean,
    verifyPhone: boolean = false
  ) => {
    if (verifyPhone) setLoginType("verifyPhone");
    else if (success) setLoginType("login");
  };

  const getAuthForm = () => {
    switch (loginType) {
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
            onResetPassword={() => setLoginType("resetPassword")}
          />
        );
    }
  };

  return (
    <Modal
      dialogClassName={styles.dialog}
      contentClassName={styles.auth}
      show={openModal || show}
      onHide={handleHide}
      centered
    >
      <Modal.Body style={{ padding: "3rem" }}>{getAuthForm()}</Modal.Body>
      {["login", "register"].includes(loginType) ? (
        <Modal.Footer className="justify-content-center">
          <a
            href="#"
            className="text-primary"
            onClick={() =>
              setLoginType(loginType === "login" ? "register" : "login")
            }
          >
            {loginType === "login" ? "Зарегистрироваться" : "Войти"}
          </a>
        </Modal.Footer>
      ) : null}
    </Modal>
  );
};

export default Auth;
