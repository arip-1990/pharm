import { useFormik } from "formik";
import { FC } from "react";
import { useAuth } from "../../hooks/useAuth";
import axios from "axios";
import { useNotification } from "../../hooks/useNotification";

type Props = {
  onSubmit: (success: boolean) => void;
};

const Login: FC<Props> = ({ onSubmit }) => {
  const { login } = useAuth();
  const notification = useNotification();

  const formik = useFormik({
    initialValues: { login: "", password: "" },
    onSubmit: async (values) => {
      try {
        await login(values.login, values.password);
      } catch (error) {
        if (axios.isAxiosError(error)) {
          notification("error", error.response.data.message);
        }
        console.log(error?.response.data);
      }

      onSubmit(true);
    },
  });

  return (
    <form onSubmit={formik.handleSubmit}>
      <div className="mb-3">
        <label htmlFor="login" className="form-label">
          Логин
        </label>
        <input
          id="login"
          name="login"
          type="text"
          className="form-control"
          onChange={formik.handleChange}
          value={formik.values.login}
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
          <button type="submit" className="btn btn-primary">
            Войти
          </button>
        </span>
      </div>
    </form>
  );
};

export default Login;
