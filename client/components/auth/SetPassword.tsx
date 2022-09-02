import { useFormik } from "formik";
import { FC, useState } from "react";
import axios from "axios";
import api from "../../lib/api";
import { useNotification } from "../../hooks/useNotification";

type Props = {
  onSubmit: (success: boolean) => void;
};

const SetPassword: FC<Props> = ({ onSubmit }) => {
  const notification = useNotification();
  const [loading, setLoading] = useState<boolean>(false);

  const formik = useFormik({
    initialValues: { password: "" },
    onSubmit: async ({ password }) => {
      setLoading(true);
      try {
        await api.post("auth/set-password", { password });
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
          placeholder="*Укажите пароль"
          className="form-control"
          onChange={formik.handleChange}
          value={formik.values.password}
        />
      </div>
      <div className="text-center">
        <button type="submit" className="btn btn-primary" disabled={loading}>
          Подтвердить
        </button>
      </div>
    </form>
  );
};

export default SetPassword;
