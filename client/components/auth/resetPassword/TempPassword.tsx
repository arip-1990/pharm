import { FC, useState } from "react";
import { useFormik } from "formik";
import { useNotification } from "../../../hooks/useNotification";
import axios from "axios";
import api from "../../../lib/api";

import styles from "../Auth.module.scss";

type Props = {
  onSubmit: (success: boolean) => void;
};

const TempPassword: FC<Props> = ({ onSubmit }) => {
  const notification = useNotification();
  const [loading, setLoading] = useState<boolean>(false);

  const formik = useFormik({
    initialValues: { password: "" },
    onSubmit: async ({ password }) => {
      setLoading(true);
      try {
        await api.post("auth/reset/password/validate", { password });
        onSubmit(true);
      } catch (error) {
        if (axios.isAxiosError(error)) {
          notification("error", error.response.data.message);
        }

        console.log(error?.response.data);
        onSubmit(false);
      }
      setLoading(false);
    },
  });

  return (
    <form onSubmit={formik.handleSubmit}>
      <div className="mb-3">
        <input
          name="password"
          placeholder="*Введите пароль"
          className="form-control"
          onChange={formik.handleChange}
          value={formik.values.password}
        />
      </div>
      <div className="text-center mt-4">
        <button type="submit" className={styles.button} disabled={loading}>
          Отправить
        </button>
      </div>
    </form>
  );
};

export default TempPassword;
