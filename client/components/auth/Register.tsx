import { useFormik } from "formik";
import axios from "axios";
import api from "../../lib/api";
import { useNotification } from "../../hooks/useNotification";
import { useState } from "react";
import { IMaskInput } from "react-imask";

interface PropsType {
  onSubmit: (success: boolean) => void;
}

const Register = ({ onSubmit }: PropsType) => {
  const notification = useNotification();
  const [loading, setLoading] = useState<boolean>(false);

  const formik = useFormik({
    initialValues: {
      cardNumber: "",
      lastName: "",
      firstName: "",
      middleName: "",
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
    <form onSubmit={formik.handleSubmit}>
      <div className="mb-3">
        <label htmlFor="cardNumber" className="form-label">
          Номер карты
        </label>
        <input
          id="cardNumber"
          name="cardNumber"
          type="text"
          className="form-control"
          onChange={formik.handleChange}
          value={formik.values.cardNumber}
        />
      </div>
      <div className="mb-3">
        <label htmlFor="lastName" className="form-label">
          Фамилия
        </label>
        <input
          id="lastName"
          name="lastName"
          type="text"
          className="form-control"
          onChange={formik.handleChange}
          value={formik.values.lastName}
        />
      </div>
      <div className="mb-3">
        <label htmlFor="firstName" className="form-label">
          Имя
        </label>
        <input
          id="firstName"
          name="firstName"
          type="text"
          className="form-control"
          onChange={formik.handleChange}
          value={formik.values.firstName}
        />
      </div>
      <div className="mb-3">
        <label htmlFor="middleName" className="form-label">
          Отчество
        </label>
        <input
          id="middleName"
          name="middleName"
          type="text"
          className="form-control"
          onChange={formik.handleChange}
          value={formik.values.middleName}
        />
      </div>
      <div className="mb-3">
        <label htmlFor="email" className="form-label">
          Email
        </label>
        <input
          id="email"
          name="email"
          type="email"
          className="form-control"
          onChange={formik.handleChange}
          value={formik.values.email}
        />
      </div>
      <div className="mb-3">
        <label htmlFor="phone" className="form-label">
          Мобильный телефон
        </label>
        <IMaskInput
          mask={"+{7} (000) 000-00-00"}
          id="phone"
          name="phone"
          className="form-control"
          onChange={formik.handleChange}
          onInput={formik.handleChange}
          value={formik.values.phone}
        />
      </div>
      <div className="mb-3">
        <label htmlFor="birthDate" className="form-label">
          Дата рождения
        </label>
        <input
          id="birthDate"
          name="birthDate"
          type="date"
          className="form-control"
          onChange={formik.handleChange}
          value={formik.values.birthDate}
        />
      </div>
      <div className="mb-3">
        <label htmlFor="gender" className="form-label">
          Пол
        </label>
        <select
          id="gender"
          name="gender"
          className="form-select"
          onChange={formik.handleChange}
          value={formik.values.gender}
        >
          <option value={0}>Не указан</option>
          <option value={1}>Мужской</option>
          <option value={2}>Женский</option>
        </select>
      </div>
      <div className="mb-3">
        <label htmlFor="password" className="form-label">
          Пароль
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
      <div className="row mt-3">
        <div className="col text-center">
          <button type="submit" className="btn btn-primary" disabled={loading}>
            Зарегистрироваться
          </button>
        </div>
      </div>
    </form>
  );
};

export default Register;
