import { useFormik } from "formik";
import { FC, MouseEvent, useEffect, useState } from "react";
import axios from "axios";
import { useTimer } from "react-timer-hook";
import api from "../../lib/api";
import { useNotification } from "../../hooks/useNotification";

type Props = {
  onSubmit: (success: boolean, setPassword?: boolean) => void;
};

const CheckSms: FC<Props> = ({ onSubmit }) => {
  const notification = useNotification();
  const [loading, setLoading] = useState<boolean>(false);
  const [attempt, setAttempt] = useState<number>(2);
  const [expiryTime, setExpiryTime] = useState(new Date());
  const { seconds, isRunning, start } = useTimer({
    expiryTimestamp: expiryTime,
  });

  console.log(seconds);

  useEffect(() => {
    if (attempt) {
      const sec = attempt === 2 ? 30 : 60;
      const date = new Date();
      date.setSeconds(expiryTime.getSeconds() + sec);
      setExpiryTime(date);
      start();
    }
  }, [attempt]);

  const handleResendSms = async (e: MouseEvent) => {
    e.preventDefault();
    if (!isRunning) {
      try {
        await api.get("auth/verify/phone");
        setAttempt(attempt - 1);
      } catch (error) {
        if (axios.isAxiosError(error)) {
          notification("error", error.response.data.message);
        }

        console.log(error?.response.data);
      }
    }
  };

  const formik = useFormik({
    initialValues: { smsCode: "" },
    onSubmit: async ({ smsCode }) => {
      let setPassword = false;
      setLoading(true);
      try {
        await api.post("auth/verify/phone", { smsCode });
        onSubmit(true);
      } catch (error) {
        if (axios.isAxiosError(error)) {
          if (error.response.data.code === 100023) setPassword = true;
          notification("error", error.response.data.message);
        }

        console.log(error?.response.data);
        onSubmit(false, setPassword);
      }
      setLoading(false);
    },
  });

  return (
    <form onSubmit={formik.handleSubmit}>
      <div className="mb-2">
        <label htmlFor="smsCode" className="form-label">
          Код подтверждения
        </label>
        <input
          id="smsCode"
          name="smsCode"
          type="text"
          className="form-control"
          onChange={formik.handleChange}
          value={formik.values.smsCode}
        />
      </div>
      {/* <div className="text-center mb-2">
        <a href="#" onClick={handleResendSms}>
          Повторно отправить код {seconds || null}
        </a>
      </div> */}
      <div className="text-center">
        <button type="submit" className="btn btn-primary" disabled={loading}>
          Подтвердить телефон
        </button>
      </div>
    </form>
  );
};

export default CheckSms;
