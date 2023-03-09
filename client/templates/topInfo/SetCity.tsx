import { FC } from "react";
import { Dropdown } from "react-bootstrap";
import { useFetchCitiesQuery } from "../../lib/cityService";

import styles from './TopInfo.module.scss';

type Props = {
  city?: string;
  setCity: (city: string) => void;
};

const SetCity: FC<Props> = ({ city, setCity }) => {
  const { data } = useFetchCitiesQuery();

  const handleSetCity = async (newCity: string) => {
    setCity(newCity);
  };

  if (data) {
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
            {city || data[0]?.name}
          </Dropdown.Toggle>

          <Dropdown.Menu>
            {data.map((item) => (
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
          <h5 className="w-100 mb-3">Ваш город {data[0]?.name}?</h5>
          <button
            className="btn btn-primary btn-sm"
            onClick={() => handleSetCity(data[0]?.name)}
          >
            Да, все верно
          </button>
          <button className="btn btn-outline-secondary btn-sm">
            Выбрать другой
          </button>
        </div>
      </div>
    );
  }

  return null;
};

export { SetCity };
