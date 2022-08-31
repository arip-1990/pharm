import { useFormik } from "formik";
import { FC, useState } from "react";
import { useAuth } from "../../hooks/useAuth";
import axios from "axios";
import { useNotification } from "../../hooks/useNotification";
import { IMaskInput } from "react-imask";
import { useRouter } from "next/router";

type Props = {
  onSubmit: (success: boolean, other?: "checkSms" | "setPassword") => void;
};

const Login: FC<Props> = ({ onSubmit }) => {
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
          if (error.response.data.code === 100033) tmp = "checkSms";
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

  return (
    <form onSubmit={formik.handleSubmit}>
      <div className="mb-3">
        <label htmlFor="login" className="form-label">
          Телефон
        </label>
        <IMaskInput
          mask={"+{7} (000) 000-00-00"}
          id="login"
          name="login"
          className="form-control"
          onChange={formik.handleChange}
          value={formik.values.login}
          onInput={formik.handleChange}
        />
      </div>
      <div className="mb-3">
        <label htmlFor="password" className="form-label">
          Пароль
        </label>
        <input
          id="password"
          name="password"
          type="password"
          className="form-control"
          onChange={formik.handleChange}
          value={formik.values.password}
        />
      </div>
      <div className="row align-items-center">
        <a className="col-7" href="#">
          Забыли пароль?
        </a>
        <span className="col-5 text-end">
          <button type="submit" className="btn btn-primary" disabled={loading}>
            Войти
          </button>
        </span>
      </div>
    </form>
  );
};

export default Login;
