import { useFormik } from "formik";
import { FC } from "react";

type Props = {
  onSubmit: (values: { smsCode: string }) => void;
};

const CheckSms: FC<Props> = ({ onSubmit }) => {
  const formik = useFormik({
    initialValues: { smsCode: "" },
    onSubmit: (values) => {
      onSubmit(values);
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
