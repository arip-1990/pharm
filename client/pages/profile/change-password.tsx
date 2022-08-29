import { useFormik } from "formik";
import { useRouter } from "next/router";
import { FC } from "react";
import Layout from "../../components/layout";
import BaseProfile from "../../components/profile";
import { useAuth } from "../../hooks/useAuth";

const ChangePassword: FC = () => {
  const { user } = useAuth();
  const router = useRouter();

  console.log(user);

  const formik = useFormik({
    initialValues: { oldPassword: "", password: "", confirmedPassword: "" },
    onSubmit: (values) => {
      console.log(values);
    },
  });

  return (
    <Layout>
      <BaseProfile title="Изменить пароль">
        <form onSubmit={formik.handleSubmit}>
          <div className="mb-3">
            <label htmlFor="oldPassword" className="form-label">
              Старый пароль
            </label>
            <input
              id="oldPassword"
              name="oldPassword"
              type="password"
              className="form-control"
              onChange={formik.handleChange}
              value={formik.values.oldPassword}
            />
          </div>
          <div className="mb-3">
            <label htmlFor="password" className="form-label">
              Новый пароль
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
          <div className="mb-3">
            <label htmlFor="confirmedPassword" className="form-label">
              Повторить новый пароль
            </label>
            <input
              id="confirmedPassword"
              name="confirmedPassword"
              type="password"
              className="form-control"
              onChange={formik.handleChange}
              value={formik.values.confirmedPassword}
            />
          </div>
          <div className="row align-items-center">
            <span className="col-5 text-end">
              <button type="submit" className="btn btn-primary">
                Сохранить
              </button>
            </span>
          </div>
        </form>
      </BaseProfile>
    </Layout>
  );
};

export default ChangePassword;
