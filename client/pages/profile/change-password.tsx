import axios from "axios";
import { Formik, FormikHelpers, useField } from "formik";
import { FC } from "react";
import Layout from "../../components/layout";
import BaseProfile from "../../components/profile";
import api from "../../lib/api";
import { useNotification } from "../../hooks/useNotification";

const TextField = ({ label, ...props }: any) => {
  const [field, meta, helpers] = useField(props);
  const style = {
    width: "100%",
    marginTop: "0.25rem",
    fontSize: "0.85rem",
    color: "#dc3545",
  };

  return (
    <>
      <label htmlFor={field.name} className="form-label">
        {label}
      </label>
      <input className="form-control" id={field.name} {...field} {...props} />
      {meta.touched && meta.error ? (
        <div style={style}>{meta.error}</div>
      ) : null}
    </>
  );
};

const ChangePassword: FC = () => {
  const notification = useNotification();

  const handleSubmit = async (values: any, actions: FormikHelpers<any>) => {
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
  };

  const handleValidate = (values: any) => {
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
  };

  return (
    <Layout>
      <BaseProfile title="Изменить пароль">
        <Formik
          initialValues={{
            oldPassword: "",
            password: "",
            password_confirmation: "",
          }}
          onSubmit={handleSubmit}
          validate={handleValidate}
        >
          {({ isValid, handleSubmit }) => (
            <form
              onSubmit={handleSubmit}
              style={{ maxWidth: 320, margin: "auto" }}
            >
              <div className="mb-3">
                <TextField
                  type="password"
                  name="oldPassword"
                  label="Старый пароль"
                />
              </div>
              <div className="mb-3">
                <TextField
                  type="password"
                  name="password"
                  label="Новый пароль"
                />
              </div>
              <div className="mb-3">
                <TextField
                  type="password"
                  name="password_confirmation"
                  label="Повторить новый пароль"
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
          )}
        </Formik>
      </BaseProfile>
    </Layout>
  );
};

export default ChangePassword;
