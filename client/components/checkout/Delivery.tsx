import { ChangeEvent, FC, useCallback, useState } from "react";
import classNames from "classnames";
import Accordion from "../accordion";
import { FormikErrors } from "formik";

interface Props {
  recipe: boolean;
  defaultValue?: number;
  deliveryValues?: {
    city?: string;
    street?: string;
    house?: string;
    entrance?: number;
    floor?: number;
    apt?: number;
    service_to_door?: boolean;
  };
  errors: FormikErrors<any>;
  onChange?: (e: any) => void;
}

const ErrorField: FC<{ name: string; errors: FormikErrors<any> }> = ({
  name,
  errors,
}) => {
  const style = {
    width: "100%",
    marginTop: "0.25rem",
    fontSize: "0.85rem",
    color: "#dc3545",
  };

  return errors[name] ? <div style={style}>{errors[name]}</div> : null;
};

const Delivery: FC<Props> = ({
  recipe,
  defaultValue = 0,
  deliveryValues,
  errors,
  onChange,
}) => {
  const [value, setValue] = useState<number>(defaultValue);

  const handleChecked = useCallback(
    (e: ChangeEvent<HTMLInputElement>) => {
      setValue(Number(e.target.value));
      onChange(e);
    },
    [defaultValue]
  );

  return (
    <Accordion activeKey={value.toString()}>
      <Accordion.Item eventKey="0">
        <div className="row">
          <div className="col-10 col-lg-6 col-xl-5 col-xxl-4 offset-1 offset-lg-0 offset-xl-1">
            <Accordion.Header
              as="label"
              className={classNames("radio-button", { active: value === 0 })}
            >
              <input
                type="radio"
                name="delivery"
                className="radio-button_pin"
                value={0}
                checked={value === 0}
                onChange={handleChecked}
              />
              <p className="radio-button_text">
                Самовывоз<span>Бесплатно</span>
              </p>
            </Accordion.Header>
          </div>
          <div className="col-10 col-lg-6 col-xl-5 col-xxl-5 offset-1 offset-lg-0 offset-xl-1">
            Вы можете совершить покупку и забрать свой заказ самостоятельно,
            приехав в аптеку.
          </div>
        </div>
        <Accordion.Body />
      </Accordion.Item>
      <Accordion.Item eventKey="1">
        <div className="row">
          <div className="col-10 col-lg-6 col-xl-5 col-xxl-4 offset-1 offset-lg-0 offset-xl-1">
            <Accordion.Header
              as="label"
              className={classNames("radio-button", { active: value === 1 })}
            >
              <input
                type="radio"
                name="delivery"
                className="radio-button_pin"
                value={1}
                checked={value === 1}
                onChange={handleChecked}
                disabled={recipe}
              />
              <p className="radio-button_text">
                Доставка<span>Указать адрес доставки.</span>
              </p>
            </Accordion.Header>
          </div>
          <div className="col-10 col-lg-6 col-xl-5 col-xxl-5 offset-1 offset-lg-0 offset-xl-1">
            Доставка осуществляется с 9:00 до 21:00, без выходных. Доставка
            осуществляется по тарифам такси.
          </div>
        </div>
        <Accordion.Body>
          <>
            <div className="row">
              <div className="col-sm-3 offset-xl-1">
                <label htmlFor="city" className="form-label">
                  Город
                </label>
                <input
                  name="city"
                  className="form-control"
                  id="city"
                  defaultValue={deliveryValues.city}
                  disabled
                />
              </div>
              <div className="col-sm-5">
                <label htmlFor="street" className="form-label">
                  Улица
                </label>
                <input
                  name="street"
                  className="form-control"
                  id="street"
                  placeholder="Улица"
                  value={deliveryValues.street}
                  onChange={onChange}
                />
                <ErrorField name="street" errors={errors} />
              </div>
              <div className="col-sm-2">
                <label htmlFor="house" className="form-label">
                  Дом
                </label>
                <input
                  name="house"
                  className="form-control"
                  id="house"
                  placeholder="Дом"
                  value={deliveryValues.house}
                  onChange={onChange}
                />
                <ErrorField name="house" errors={errors} />
              </div>
            </div>
            <div className="row my-3">
              <div className="col-sm-3 offset-xl-1">
                <label htmlFor="entrance" className="form-label">
                  Подъезд
                </label>
                <input
                  name="entrance"
                  className="form-control"
                  id="entrance"
                  placeholder="Подъезд"
                  value={deliveryValues.entrance}
                  onChange={onChange}
                />
                <ErrorField name="entrance" errors={errors} />
              </div>
              <div className="col-sm-4">
                <label htmlFor="floor" className="form-label">
                  Этаж
                </label>
                <input
                  name="floor"
                  className="form-control"
                  id="floor"
                  placeholder="Этаж"
                  value={deliveryValues.floor}
                  onChange={onChange}
                />
                <ErrorField name="floor" errors={errors} />
              </div>
              <div className="col-sm-3">
                <label htmlFor="apt" className="form-label">
                  Квартира
                </label>
                <input
                  name="apt"
                  className="form-control"
                  id="apt"
                  placeholder="Квартира"
                  value={deliveryValues.apt}
                  onChange={onChange}
                />
                <ErrorField name="apt" errors={errors} />
              </div>
            </div>
            <div className="row">
              <div className="col-sm-10 offset-1 offset-lg-0 offset-xl-1">
                <input
                  className="form-check-input"
                  type="checkbox"
                  name="service_to_door"
                  id="service_to_door"
                  checked={deliveryValues.service_to_door}
                  onChange={onChange}
                />
                <label
                  className="form-check-label ms-2"
                  htmlFor="service_to_door"
                >
                  Доставка до двери
                </label>
                <ErrorField name="service_to_door" errors={errors} />
              </div>
            </div>
          </>
        </Accordion.Body>
      </Accordion.Item>
    </Accordion>
  );
};

export default Delivery;
