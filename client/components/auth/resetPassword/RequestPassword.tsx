import { FC, useState } from "react";
import { useFormik } from "formik";
import { IMaskInput } from "react-imask";
import { useNotification } from "../../../hooks/useNotification";
import axios from "axios";
import api from "../../../lib/api";

import styles from "../Auth.module.scss";

type Props = {
  onSubmit: (success: boolean, verifyPhone?: boolean) => void;
};

const RequestPassword: FC<Props> = ({ onSubmit }) => {
  const notification = useNotification();
  const [loading, setLoading] = useState<boolean>(false);

  const formik = useFormik({
    initialValues: { phone: "" },
    onSubmit: async ({ phone }) => {
      setLoading(true);
      let verifyPhone = false;
      try {
        await api.get("auth/reset/password", {
          params: { phone: phone.replace(/[^0-9]/g, "") },
        });
        notification("success", "Вам отправлен пароль");
        onSubmit(true);
      } catch (error) {
        if (axios.isAxiosError(error)) {
          if (error.response.data.code === 100023) verifyPhone = true;
          notification("error", error.response.data.message);
        }

        console.log(error?.response.data);
        onSubmit(false, verifyPhone);
      }
      setLoading(false);
    },
  });

  return (
    <form onSubmit={formik.handleSubmit}>
      <div className="mb-3">
        <IMaskInput
          mask={"+{7} (000) 000-00-00"}
          name="phone"
          placeholder="*Мобильный телефон"
          className="form-control"
          onChange={formik.handleChange}
          onInput={formik.handleChange}
          value={formik.values.phone}
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

export default RequestPassword;
