import { useFormik } from "formik";
import { FC } from "react";
import axios from "axios";
import api from "../../lib/api";
import { useNotification } from "../../hooks/useNotification";

type Props = {
  onSubmit: (success: boolean) => void;
};

const CheckSms: FC<Props> = ({ onSubmit }) => {
  const notification = useNotification();

  const formik = useFormik({
    initialValues: { smsCode: "" },
    onSubmit: async ({ smsCode }) => {
      try {
        await api.post("auth/checkSms", { smsCode });
        onSubmit(true);
      } catch (error) {
        if (axios.isAxiosError(error)) {
          notification("error", error.response.data.message);
        }

        console.log(error?.response.data);
        onSubmit(false);
      }
    },
  });

  return (
    <form onSubmit={formik.handleSubmit}>
      <div className="mb-3">
        <label htmlFor="checkSms" className="form-label">
          Код подтверждения
        </label>
        <input
          id="checkSms"
          name="checkSms"
          type="text"
          className="form-control"
          onChange={formik.handleChange}
          value={formik.values.smsCode}
        />
      </div>
      <div className="text-center">
        <button type="submit" className="btn btn-primary">
          Подтвердить телефон
        </button>
      </div>
    </form>
  );
};

export default CheckSms;
