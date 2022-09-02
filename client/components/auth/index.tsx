import { FC, useEffect, useState } from "react";
import { Modal } from "react-bootstrap";
import Login from "./Login";
import Register from "./Register";
import CheckSms from "./CheckSms";
import SetPassword from "./SetPassword";
import { useRouter } from "next/router";

type AuthType = "login" | "register" | "checkSms" | "setPassword";

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

  const handleLogin = (
    success: boolean,
    other: "checkSms" | "setPassword" = null
  ) => {
    if (other) {
      setLoginType(other);
    } else {
      onHide();
    }
  };

  const handleRegister = (success: boolean) => {
    if (success) {
      setLoginType("checkSms");
    } else {
      onHide();
    }
  };

  const handleCheckSms = (success: boolean) => {
    if (success) onHide();
  };

  const handleSetPassword = (success: boolean, setPassword?: boolean) => {
    if (setPassword) setLoginType("setPassword");
    else if (success) onHide();
  };

  return (
    <Modal size="sm" show={openModal || show} onHide={onHide} centered>
      <Modal.Body>
        {loginType === "login" ? (
          <Login onSubmit={handleLogin} />
        ) : loginType === "register" ? (
          <Register onSubmit={handleRegister} />
        ) : loginType === "checkSms" ? (
          <CheckSms onSubmit={handleCheckSms} />
        ) : (
          <SetPassword onSubmit={handleSetPassword} />
        )}
      </Modal.Body>
      {loginType !== "checkSms" && loginType !== "setPassword" ? (
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
