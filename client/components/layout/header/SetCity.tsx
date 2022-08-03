import { FC } from "react";
import { Dropdown } from "react-bootstrap";
import { useCookies } from "react-cookie";
import { useFetchCitiesQuery } from "../../../lib/cityService";

type Props = {
  className?: string;
};

const SetCity: FC<Props> = ({ className }) => {
  const [{ city }, setCity] = useCookies(["city"]);
  const { data } = useFetchCitiesQuery();
  let classes = ["menu-city"];
  if (className) classes = classes.concat(className.split(" "));

  if (data) {
    return (
      <div className={classes.join(" ")}>
        <Dropdown onSelect={(eventKey) => setCity("city", eventKey)}>
          <span>Ваш город:</span>
          <Dropdown.Toggle
            variant="success"
            id="city"
            as="a"
            style={{ cursor: "pointer" }}
          >
            {city || data[0].name}
          </Dropdown.Toggle>

          <Dropdown.Menu>
            {data.map((item) => (
              <Dropdown.Item
                key={item.id}
                href="#"
                eventKey={item.id}
                active={city === item.name}
              >
                {item.name}
              </Dropdown.Item>
            ))}
          </Dropdown.Menu>
        </Dropdown>
        <div className="city-choose" style={city ? {} : { display: "flex" }}>
          <h5 className="w-100 mb-3">Ваш город {data[0].name}?</h5>
          <button
            className="btn btn-primary btn-sm"
            onClick={() => setCity("city", data[0].name)}
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
