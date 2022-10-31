import axios from "axios";
import { FormikErrors, FormikHelpers, useFormik } from "formik";
import { FC, MouseEvent, useCallback, useEffect, useState } from "react";
import { useNotification } from "../../hooks/useNotification";
import api from "../../lib/api";
import { useIMask } from "react-imask";

import styles from "./Auth.module.scss";
import classNames from "classnames";

type RegisterType = "register" | "verifyPhone";

interface Values {
  smsCode: string;
  fullName: string;
  email: string;
  phone: string;
  birthDate: string;
  gender: number;
  password: string;
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

const Register: FC<Props> = ({ switchAuthType }) => {
  const [active, setActive] = useState<boolean>(false);
  const [maskOpts, setMaskOpts] = useState<any>({
    mask: "+{7} (000) 000-00-00",
  });
  const { ref, value, setValue } = useIMask(
    maskOpts /* { onAccept, onComplete } */
  );
  const [type, setType] = useState<RegisterType>("register");
  const notification = useNotification();

  useEffect(() => {
    addEventListener("mouseup", handleUp);

    return () => removeEventListener("mouseup", handleUp);
  }, []);

  const formik = useFormik({
    initialValues: {
      smsCode: "",
      fullName: "",
      email: "",
      phone: "",
      birthDate: "",
      gender: 0,
      password: "",
    },
    onSubmit: async (values: Values, actions: FormikHelpers<Values>) => {
      try {
        if (type === "register") {
          await handleRegister(values);
          setType("verifyPhone");
        } else if (type === "verifyPhone") {
          await handleVerifyPhone(values);
          switchAuthType("login");
          notification("success", "Телефон верифицирован");
        }
      } catch (error) {
        if (axios.isAxiosError(error)) {
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

  const handleRegister = async (values: Values) =>
    new Promise<void>(async (resolve, reject) => {
      values.phone = values.phone.replace(/[^0-9]/g, "");
      try {
        await api.post("auth/register", { ...values });
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
        return "Чтобы начать экономить укажите свои данные";
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
      default:
        form = [
          <div key="fullName" className="mb-3">
            <input
              name="fullName"
              placeholder="*ФИО"
              className="form-control"
              onChange={formik.handleChange}
              value={formik.values.fullName}
              required
            />
            <ErrorField name="fullName" errors={formik.errors} />
          </div>,
          <div key="phone" className="mb-3">
            <input
              ref={ref}
              name="phone"
              placeholder="*Мобильный телефон"
              className="form-control"
              onChange={(e: any) => {
                formik.handleChange(e);
                setValue(e);
              }}
              onInput={formik.handleChange}
              value={value}
              required
            />
            <ErrorField name="phone" errors={formik.errors} />
          </div>,
          <div key="email" className="mb-3">
            <input
              name="email"
              type="email"
              placeholder="Email"
              className="form-control"
              onChange={formik.handleChange}
              value={formik.values.email}
            />
            <ErrorField name="email" errors={formik.errors} />
          </div>,
          <div key="birthDate" className="mb-3">
            <input
              name="birthDate"
              type="date"
              placeholder="*Дата рождения"
              className="form-control"
              onChange={formik.handleChange}
              value={formik.values.birthDate}
              required
            />
            <ErrorField name="birthDate" errors={formik.errors} />
          </div>,
          <div key="gender" className="mb-3">
            <select
              name="gender"
              className="form-select"
              onChange={formik.handleChange}
              value={formik.values.gender}
            >
              <option value={0}>Выберите пол</option>
              <option value={1}>Мужской</option>
              <option value={2}>Женский</option>
            </select>
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

        <div className="text-center mt-4">
          <button
            type="submit"
            data-text="Отправить"
            className={classNames(styles.button, { [styles.active]: active })}
            onMouseDown={handleDown}
            disabled={formik.isSubmitting}
          />
        </div>
      </form>
    </div>
  );
};

export default Register;
