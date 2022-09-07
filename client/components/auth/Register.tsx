import { useFormik } from "formik";
import axios from "axios";
import api from "../../lib/api";
import { useNotification } from "../../hooks/useNotification";
import { useState } from "react";
import { IMaskInput } from "react-imask";
import styles from "./Auth.module.scss";

interface PropsType {
  onSubmit: (success: boolean) => void;
}

const Register = ({ onSubmit }: PropsType) => {
  const notification = useNotification();
  const [loading, setLoading] = useState<boolean>(false);

  const formik = useFormik({
    initialValues: {
      cardNumber: "",
      fullName: "",
      email: "",
      phone: "",
      birthDate: "",
      gender: 0,
      password: "",
    },
    onSubmit: async (values) => {
      setLoading(true);
      values.phone = values.phone.replace(/[^0-9]/g, "");
      try {
        await api.post("auth/register", { ...values });

        onSubmit(true);
      } catch (error) {
        if (axios.isAxiosError(error)) {
          if (error.response.status === 422) {
            let messages: { title: string; message: string }[] = [];
            const err = error.response.data as {
              message: string;
              errors: object;
            };
            Object.keys(err.errors).forEach((key) => {
              messages.push({ title: key, message: err.errors[key] });
            });

            notification("error", messages);
          } else notification("error", error.response.data.message);
        }

        console.log(error?.response.data);

        onSubmit(false);
        setLoading(false);
      }
    },
  });

  return (
    <div>
      <h2 className="text-center mb-4">
        Чтобы начать экономить укажите свои данные
      </h2>
      <form onSubmit={formik.handleSubmit}>
        <div className="mb-3">
          <input
            name="cardNumber"
            placeholder="Номер карты"
            className="form-control"
            onChange={formik.handleChange}
            value={formik.values.cardNumber}
          />
        </div>
        <div className="mb-3">
          <input
            name="fullName"
            placeholder="*ФИО"
            className="form-control"
            onChange={formik.handleChange}
            value={formik.values.fullName}
          />
        </div>
        <div className="mb-3">
          <input
            name="email"
            type="email"
            placeholder="*Email"
            className="form-control"
            onChange={formik.handleChange}
            value={formik.values.email}
          />
        </div>
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
        <div className="mb-3">
          <input
            name="birthDate"
            type="date"
            placeholder="*Дата рождения"
            className="form-control"
            onChange={formik.handleChange}
            value={formik.values.birthDate}
          />
        </div>
        <div className="mb-3">
          <select
            name="gender"
            className="form-select"
            onChange={formik.handleChange}
            value={formik.values.gender}
          >
            <option value={0}>Выберите пол</option>
            <option value={1}>Мужской</option>
            <option value={2}>Женский</option>
          </select>
        </div>
        <div className="mb-3">
          <input
            name="password"
            type="password"
            placeholder="*Пароль"
            className="form-control"
            onChange={formik.handleChange}
            value={formik.values.password}
          />
        </div>
        <div className="row mt-4">
          <div className="col text-center">
            <button type="submit" className={styles.button} disabled={loading}>
              Отправить
            </button>
          </div>
        </div>
      </form>
    </div>
  );
};

export default Register;
