import axios from "axios";
import { FormikErrors, FormikHelpers, useFormik } from "formik";
import { FC } from "react";

import Layout from "../../templates";
import BaseProfile from "../../templates/profile";
import api from "../../lib/api";
import { useNotification } from "../../hooks/useNotification";

interface Values {
  oldPassword: string;
  password: string;
  password_confirmation: string;
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

const ChangePassword: FC = () => {
  const notification = useNotification();

  const formik = useFormik({
    initialValues: { oldPassword: "", password: "", password_confirmation: "" },
    onSubmit: async (values: Values, actions: FormikHelpers<Values>) => {
      try {
        await api.put("user/update-password", { ...values });
        actions.resetForm();
        notification("success", "Пароль изменен");
      } catch (error) {
        if (axios.isAxiosError(error)) {
          if (error.response.data.code === 100023)
            notification("error", error.response.data.message);
        }

        console.log(error?.response.data);
      }
      actions.setSubmitting(false);
    },
    validate: (values: Values) => {
      const errors = {
        oldPassword: "",
        password: "",
        password_confirmation: "",
      };
      if (
        values.password_confirmation &&
        values.password_confirmation !== values.password
      ) {
        errors.password_confirmation = "Пароли не совпадают";
      }
      return errors;
    },
  });

  return (
    <Layout title="Изменить пароль - Сеть аптек 120/80">
      <BaseProfile title="Изменить пароль">
        <form
          onSubmit={formik.handleSubmit}
          style={{ maxWidth: 320, margin: "3rem auto" }}
        >
          <div className="mb-3">
            <input
              className="form-control"
              style={{ fontSize: '0.75rem' }}
              type="password"
              name="oldPassword"
              placeholder="*Старый пароль"
              onChange={formik.handleChange}
              value={formik.values.oldPassword}
              required
            />
            <ErrorField name="oldPassword" errors={formik.errors} />
          </div>
          <div className="mb-3">
            <input
              className="form-control"
              style={{ fontSize: '0.75rem' }}
              type="password"
              name="password"
              placeholder="*Новый пароль"
              onChange={formik.handleChange}
              value={formik.values.password}
              required
            />
            <ErrorField name="password" errors={formik.errors} />
          </div>
          <div className="mb-3">
            <input
              className="form-control"
              style={{ fontSize: '0.75rem' }}
              type="password"
              name="password_confirmation"
              placeholder="*Повторить новый пароль"
              onChange={formik.handleChange}
              value={formik.values.password_confirmation}
              required
            />
            <ErrorField name="password_confirmation" errors={formik.errors} />
          </div>
          <div style={{ textAlign: 'center' }}>
            <span className="col-5 text-end">
              <button
                type="submit"
                className="btn btn-sm btn-primary"
                disabled={formik.isSubmitting}
              >
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
