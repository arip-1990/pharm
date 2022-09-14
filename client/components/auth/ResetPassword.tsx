import axios from "axios";
import { FormikErrors, FormikHelpers, useFormik } from "formik";
import { FC, MouseEvent, useCallback, useEffect, useState } from "react";
import { useNotification } from "../../hooks/useNotification";
import api from "../../lib/api";
import { IMaskInput } from "react-imask";

import styles from "./Auth.module.scss";
import classNames from "classnames";

type ResetPasswordType = "request" | "temp" | "change" | "verifyPhone";

interface Values {
  phone: string;
  password: string;
  // password_confirmation: string;
  smsCode: string;
}

interface Props {
  switchAuthType: (type: "login") => void;
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

const ResetPassword: FC<Props> = ({ switchAuthType }) => {
  const [active, setActive] = useState<boolean>(false);
  const [type, setType] = useState<ResetPasswordType>("request");
  const notification = useNotification();

  useEffect(() => {
    addEventListener("mouseup", handleUp);

    return () => removeEventListener("mouseup", handleUp);
  }, []);

  const formik = useFormik({
    initialValues: { phone: "", password: "", smsCode: "" },
    onSubmit: async (values: Values, actions: FormikHelpers<Values>) => {
      console.log(values);
      try {
        if (type === "request") {
          await handleRequestPassword(values);
          notification("success", "Вам отправлен пароль");
          setType("temp");
        } else if (type === "temp") {
          await handleTempPassword(values);
          setType("change");
        } else if (type === "change") {
          await handleChangePassword(values);
          notification("success", "Пароль изменен успешно");
          switchAuthType("login");
        } else if (type === "verifyPhone") {
          await handleVerifyPhone(values);
          notification("success", "Телефон подтвержден");
        }
      } catch (error) {
        if (axios.isAxiosError(error)) {
          if (error.response.data.code === 100033) setType("verifyPhone");
          notification("error", error.response.data.message);
        }
        console.log(error?.response.data);
      }

      actions.resetForm();
      actions.setSubmitting(false);
    },
    // validate: (values: Values) => {
    //   const errors = {
    //     password: "",
    //     password_confirmation: "",
    //   };
    //   if (
    //     type === "change" &&
    //     values.password_confirmation &&
    //     values.password_confirmation !== values.password
    //   ) {
    //     errors.password_confirmation = "Пароли не совпадают";
    //   }
    //   return errors;
    // },
  });

  const handleRequestPassword = async (values: Values) =>
    new Promise<void>(async (resolve, reject) => {
      try {
        await api.get("auth/reset/password", {
          params: { phone: values.phone.replace(/[^0-9]/g, "") },
        });
        return resolve();
      } catch (error) {
        return reject(error);
      }
    });

  const handleTempPassword = async (values: Values) =>
    new Promise<void>(async (resolve, reject) => {
      try {
        await api.post("auth/reset/password/validate", {
          password: values.password,
        });
        return resolve();
      } catch (error) {
        return reject(error);
      }
    });

  const handleChangePassword = async (values: Values) =>
    new Promise<void>(async (resolve, reject) => {
      try {
        await api.post("auth/reset/password", { password: values.password });
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

  const getTitle = () => {
    switch (type) {
      case "verifyPhone":
        return "Подтверждение телефона";
      default:
        return "Сброс пароля";
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
      case "change":
        form = [
          <div key="password" className="mb-3">
            <input
              name="password"
              type="password"
              placeholder="*Введите новый пароль"
              className="form-control"
              onChange={formik.handleChange}
              value={formik.values.password}
              required
            />
            <ErrorField name="password" errors={formik.errors} />
          </div>,
          // <div key="password_confirmation" className="mb-3">
          //   <input
          //     className="form-control"
          //     type="password"
          //     name="password_confirmation"
          //     placeholder="*Повторить новый пароль"
          //     onChange={formik.handleChange}
          //     value={formik.values.password_confirmation}
          //     required
          //   />
          //   <ErrorField name="password_confirmation" errors={formik.errors} />
          // </div>,
        ];
        break;
      case "temp":
        form = [
          <div key="password" className="mb-3">
            <input
              name="password"
              placeholder="*Введите пароль"
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
          <div key="phone" className="mb-3">
            <IMaskInput
              mask={"+{7} (000) 000-00-00"}
              name="phone"
              placeholder="*Мобильный телефон"
              className="form-control"
              onChange={formik.handleChange}
              onInput={formik.handleChange}
              value={formik.values.phone}
              required
            />
            <ErrorField name="phone" errors={formik.errors} />
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

        <div className="text-center mt-4">
          <button
            type="submit"
            className={classNames(styles.button, { [styles.active]: active })}
            onMouseDown={handleDown}
            disabled={formik.isSubmitting}
          />
        </div>
      </form>
    </div>
  );
};

export default ResetPassword;
