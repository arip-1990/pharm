import { FC } from "react";
import { Dropdown } from "react-bootstrap";

import { ICity } from "../../models/ICity";

import styles from './TopInfo.module.scss';

interface IProps {
  city: string;
  cities: ICity[];
  setCity: (city: string) => void;
};

const SetCity: FC<IProps> = ({ city, cities, setCity }) => {
  const handleSetCity = async (newCity: string) => {
    setCity(newCity);
  };

  return (
    <div className={styles.chooseCity}>
      <Dropdown onSelect={(eventKey) => handleSetCity(eventKey)}>
        <span className={styles.city}>Ваш город: </span>
        <Dropdown.Toggle
          variant="success"
          id="city"
          as="a"
          style={{ cursor: "pointer" }}
        >
          {city}
        </Dropdown.Toggle>

        <Dropdown.Menu>
          {cities.map((item) => (
            <Dropdown.Item
              key={item.id}
              href="#"
              eventKey={item.name}
              active={city === item.name}
            >
              {item.name}
            </Dropdown.Item>
          ))}
        </Dropdown.Menu>
      </Dropdown>
      <div className="city-choose" style={city ? {} : { display: "flex" }}>
        <h5 className="w-100 mb-3">Ваш город {city}?</h5>
        <button
          className="btn btn-primary btn-sm"
          onClick={() => handleSetCity(city)}
        >
          Да, все верно
        </button>
        <button className="btn btn-outline-secondary btn-sm">
          Выбрать другой
        </button>
      </div>
    </div>
  );
};

export { SetCity };
