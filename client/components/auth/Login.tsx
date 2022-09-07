import { useFormik } from "formik";
import { FC, MouseEvent, useState } from "react";
import { useAuth } from "../../hooks/useAuth";
import axios from "axios";
import { useNotification } from "../../hooks/useNotification";
import { IMaskInput } from "react-imask";
import { useRouter } from "next/router";
import styles from "./Auth.module.scss";

type Props = {
  onSubmit: (success: boolean, other?: "verifyPhone" | "setPassword") => void;
  onResetPassword: () => void;
};

const Login: FC<Props> = ({ onSubmit, onResetPassword }) => {
  const { login } = useAuth();
  const notification = useNotification();
  const [loading, setLoading] = useState<boolean>(false);
  const router = useRouter();

  const formik = useFormik({
    initialValues: { login: "", password: "" },
    onSubmit: async (values) => {
      let tmp = null;
      setLoading(true);
      values.login = values.login.replace(/[^0-9]/g, "");
      try {
        await login(values.login, values.password);
        router.push("/profile");
      } catch (error) {
        if (axios.isAxiosError(error)) {
          if (error.response.data.code === 100033) tmp = "verifyPhone";
          if (error.response.data.code === 100023) tmp = "setPassword";
          notification("error", error.response.data.message);
        }
        console.log(error?.response.data);
      }

      if (tmp) onSubmit(false, tmp);
      else onSubmit(true);

      setLoading(false);
    },
  });

  const handleClickReset = (e: MouseEvent) => {
    e.preventDefault();
    onResetPassword();
  };

  return (
    <div>
      <h2 className="text-center mb-4">Войти</h2>
      <form onSubmit={formik.handleSubmit}>
        <div className="mb-3">
          <IMaskInput
            mask={"+{7} (000) 000-00-00"}
            name="login"
            placeholder="*Телефон"
            className="form-control"
            onChange={formik.handleChange}
            onInput={formik.handleChange}
            value={formik.values.login}
          />
        </div>
        <div className="mb-3">
          <input
            name="password"
            type="password"
            placeholder="*Пароль"
            className="form-control"
            onChange={formik.handleChange}
            value={formik.values.password}
          />
        </div>
        <div className="row align-items-center mt-4">
          <div className="col-7">
            <a href="#" onClick={handleClickReset}>
              Забыли пароль?
            </a>
          </div>
          <span className="col-5 text-end">
            <button type="submit" className={styles.button} disabled={loading}>
              Войти
            </button>
          </span>
        </div>
      </form>
    </div>
  );
};

export default Login;
