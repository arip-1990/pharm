import classNames from "classnames";
import { ChangeEvent, FC, useState } from "react";

type Props = {
  defaultValue?: number;
  onChange?: (e: any) => void;
};

const Payment: FC<Props> = ({ defaultValue = 0, onChange }) => {
  const [value, setValue] = useState<number>(defaultValue);

  const handleChecked = (e: ChangeEvent<HTMLInputElement>) => {
    setValue(Number(e.target.value));
    onChange(e);
  };

  return (
    <div className="row">
      <div className="col-10 col-lg-5 col-xl-4 offset-1 offset-lg-0 offset-xl-1">
        <label className={classNames("radio-button", { active: value === 0 })}>
          <input
            type="radio"
            name="payment"
            className="radio-button_pin"
            value={0}
            checked={value === 0}
            onChange={handleChecked}
          />
          <p className="radio-button_text">
            Оплата наличными<span>При получении</span>
          </p>
        </label>
      </div>
      <div className="col-10 col-lg-5 col-xl-4 offset-1 offset-lg-0 offset-lg-2">
        <label className={classNames("radio-button", { active: value === 1 })}>
          <input
            type="radio"
            name="payment"
            className="radio-button_pin"
            value={1}
            checked={value === 1}
            onChange={handleChecked}
          />
          <p className="radio-button_text">
            Оплата картой
            <span>
              <img
                style={{ height: "20px" }}
                src="/images/payments.png"
                alt=""
              />
            </span>
          </p>
        </label>
      </div>
    </div>
  );
};

export default Payment;
