import { FormikErrors, FormikHelpers, useFormik } from "formik";
import { FC, MouseEvent, useCallback, useEffect, useState } from "react";
import { useAuth } from "../../hooks/useAuth";
import { useNotification } from "../../hooks/useNotification";
import api from "../../lib/api";
import { useIMask } from "react-imask";
import axios from "axios";

import styles from "./Auth.module.scss";
import classNames from "classnames";
import { useRouter } from "next/router";

type LoginType = "login" | "verifyPhone" | "setPassword";

interface Values {
  login: string;
  password: string;
  smsCode: string;
}

interface Props {
  switchAuthType: (type: "register" | "resetPassword") => void;
  onHide: () => void;
}

const ErrorField: FC<{ name: string; errors: FormikErrors<Values> }> = ({
  name,
  errors,
}) => {
  const style = {
    width: "100%",
    marginTop: "0.25rem",
    fontSize: "0.85rem",
    color: "#dc3545",
  };

  return errors[name] ? <div style={style}>{errors[name]}</div> : null;
};

const Login: FC<Props> = ({ switchAuthType, onHide }) => {
  const { login } = useAuth();
  const [active, setActive] = useState<boolean>(false);
  const [maskOpts, setMaskOpts] = useState<any>({
    mask: "+{7} (000) 000-00-00",
  });
  const { ref, value, setValue } = useIMask(
    maskOpts /* { onAccept, onComplete } */
  );
  const [type, setType] = useState<LoginType>("login");
  const notification = useNotification();
  const router = useRouter();

  useEffect(() => {
    addEventListener("mouseup", handleUp);

    return () => removeEventListener("mouseup", handleUp);
  }, []);

  const formik = useFormik({
    initialValues: { login: "", password: "", smsCode: "" },
    onSubmit: async (values: Values, actions: FormikHelpers<Values>) => {
      try {
        if (type === "login") await handleLogin(values);
        else if (type === "verifyPhone") {
          await handleVerifyPhone(values);
          notification("success", "Телефон подтвержден");
        } else if (type === "setPassword") {
          await handleSetPassword(values);
          notification("success", "Пароль установлен успешно");
        }

        // router.push("/profile");
        onHide();
      } catch (error) {
        if (axios.isAxiosError(error)) {
          if (error.response.data.code === 100033) setType("verifyPhone");
          else if (error.response.data.code === 100023) setType("setPassword");

          if (error.response.status === 422) {
            for (const [key, value] of Object.entries(
              error.response.data?.errors || {}
            )) {
              if (key in values) {
                actions.setFieldError(key, value[0]);
              }
            }
          } else notification("error", error.response.data.message);
        }
        console.log(error?.response.data);
      }

      actions.resetForm();
      actions.setSubmitting(false);
    },
  });

  const handleLogin = async (values: Values) =>
    new Promise<void>(async (resolve, reject) => {
      values.login = values.login.replace(/[^0-9]/g, "");
      try {
        await login(values.login, values.password);
        return resolve();
      } catch (error) {
        return reject(error);
      }
    });

  const handleVerifyPhone = async (values: Values) =>
    new Promise<void>(async (resolve, reject) => {
      try {
        await api.post("auth/verify/phone", { smsCode: values.smsCode });
        return resolve();
      } catch (error) {
        return reject(error);
      }
    });

  const handleSetPassword = async (values: Values) =>
    new Promise<void>(async (resolve, reject) => {
      try {
        await api.post("auth/set-password", { password: values.password });
        return resolve();
      } catch (error) {
        return reject(error);
      }
    });

  const getTitle = () => {
    switch (type) {
      case "verifyPhone":
        return "Подтверждение телефона";
      case "setPassword":
        return "Установка пароля";
      default:
        return "Добро пожаловать";
    }
  };

  const generateForm = () => {
    let form = [];
    switch (type) {
      case "verifyPhone":
        form = [
          <div key="smsCode" className="mb-3">
            <input
              name="smsCode"
              placeholder="*Код подтверждения"
              className="form-control"
              onChange={formik.handleChange}
              value={formik.values.smsCode}
              required
            />
            <ErrorField name="smsCode" errors={formik.errors} />
          </div>,
        ];
        break;
      case "setPassword":
        form = [
          <div key="password" className="mb-3">
            <input
              name="password"
              placeholder="*Укажите пароль"
              className="form-control"
              onChange={formik.handleChange}
              value={formik.values.password}
              required
            />
            <ErrorField name="password" errors={formik.errors} />
          </div>,
        ];
        break;
      default:
        form = [
          <div key="login" className="mb-3">
            <input
              ref={ref}
              name="login"
              placeholder="*Телефон"
              className="form-control"
              onChange={(e: any) => {
                formik.handleChange(e);
                setValue(e);
              }}
              onInput={formik.handleChange}
              value={value}
              required
            />
            <ErrorField name="login" errors={formik.errors} />
          </div>,
          <div key="password" className="mb-3">
            <input
              name="password"
              type="password"
              placeholder="*Пароль"
              className="form-control"
              onChange={formik.handleChange}
              value={formik.values.password}
              required
            />
            <ErrorField name="password" errors={formik.errors} />
          </div>,
        ];
    }

    return form;
  };

  const handleDown = useCallback((e: MouseEvent<HTMLButtonElement>) => {
    if (e.button === 0) setActive(true);
  }, []);

  const handleUp = useCallback(() => setActive(false), []);

  return (
    <div>
      <h2 className="text-center mb-4">{getTitle()}</h2>
      <form onSubmit={formik.handleSubmit}>
        {generateForm()}

        {type === "login" ? (
          <div className="col-7">
            <a href="#" onClick={() => switchAuthType("resetPassword")}>
              Забыли пароль?
            </a>
          </div>
        ) : null}

        <div className="text-center mt-4">
          <button
            type="submit"
            data-text={type === "login" ? "Войти" : "Отправить"}
            className={classNames(styles.button, { [styles.active]: active })}
            onMouseDown={handleDown}
            disabled={formik.isSubmitting}
          />
        </div>
      </form>
    </div>
  );
};

export default Login;
