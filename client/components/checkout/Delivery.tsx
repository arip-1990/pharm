import { ChangeEvent, FC, useState } from "react";
import { useCookies } from "react-cookie";
import classNames from "classnames";
import Accordion from "../accordion";

type Props = {
  recipe: boolean;
  defaultValue?: number;
};

const Delivery: FC<Props> = ({ recipe, defaultValue = 0 }) => {
  const [{ city }] = useCookies(["city"]);
  const [value, setValue] = useState<number>(defaultValue);

  const handleChecked = (e: ChangeEvent<HTMLInputElement>) => {
    setValue(Number(e.target.value));
  };

  return (
    <Accordion defaultActiveKey={defaultValue.toString()}>
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
                // disabled={recipe}
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
                  defaultValue={city}
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
                  defaultValue="{{ old('street') }}"
                />
                {/* <p style={{fontSize: '0.75rem', fontWeight: 300}} className="text-danger">Поле обязательно для заполнения.</p> */}
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
                  defaultValue="{{ old('house') }}"
                />
                {/* <p style={{fontSize: '0.75rem', fontWeight: 300}} className="text-danger">Поле обязательно для заполнения.</p> */}
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
                  defaultValue="{{ old('entrance') }}"
                />
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
                  defaultValue="{{ old('floor') }}"
                />
              </div>
              <div className="col-sm-3">
                <label htmlFor="apartment" className="form-label">
                  Квартира
                </label>
                <input
                  name="apartment"
                  className="form-control"
                  id="apartment"
                  placeholder="Квартира"
                  defaultValue="{{ old('apartment') }}"
                />
              </div>
            </div>
            <div className="row">
              <div className="col-sm-10 offset-1 offset-lg-0 offset-xl-1">
                <input
                  className="form-check-input"
                  type="checkbox"
                  name="service_to_door"
                  defaultValue="{{ old('service_to_door') }}"
                  id="service_to_door"
                />
                <label className="form-check-label" htmlFor="service_to_door">
                  Доставка до двери
                </label>
              </div>
            </div>
          </>
        </Accordion.Body>
      </Accordion.Item>
    </Accordion>
  );
};

export default Delivery;
