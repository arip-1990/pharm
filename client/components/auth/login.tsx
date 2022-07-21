import { useFormik } from "formik";
import { FC } from "react";

type Props = {
  onSubmit: (values: { Login: string; Password: string }) => void;
};

const Login: FC<Props> = ({ onSubmit }) => {
  const formik = useFormik({
    initialValues: { Login: "", Password: "" },
    onSubmit: (values) => {
      onSubmit(values);
    },
  });

  return (
    <form onSubmit={formik.handleSubmit}>
      <div className="mb-3">
        <label htmlFor="Login" className="form-label">
          Логин
        </label>
        <input
          id="Login"
          name="Login"
          type="text"
          className="form-control"
          onChange={formik.handleChange}
          value={formik.values.Login}
        />
      </div>
      <div className="mb-3">
        <label htmlFor="Password" className="form-label">
          Пароль
        </label>
        <input
          id="Password"
          name="Password"
          type="password"
          className="form-control"
          onChange={formik.handleChange}
          value={formik.values.Password}
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
