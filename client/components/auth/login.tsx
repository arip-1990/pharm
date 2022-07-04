import { useFormik } from "formik";
import { FC } from "react";

type Props = {
  onSubmit: (values: { login: string; password: string }) => void;
};

const Login: FC<Props> = ({ onSubmit }) => {
  const formik = useFormik({
    initialValues: { login: "", password: "" },
    onSubmit: (values) => {
      onSubmit(values);
    },
  });

  return (
    <form onSubmit={formik.handleSubmit}>
      <div className="mb-3">
        <label htmlFor="login" className="form-label">
          Мобильный телефон
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
