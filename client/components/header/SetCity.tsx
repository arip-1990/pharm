import { FC, useEffect, useState } from "react";
import { Dropdown } from "react-bootstrap";
import { useCookies } from "react-cookie";
import { useFetchCitiesQuery } from "../../services/cityService";

type Props = {
  className?: string;
};

const SetCity: FC<Props> = ({ className }) => {
  const [city, setCity] = useState<string>();
  const [cookies, setCookie] = useCookies(["city"]);
  const { data } = useFetchCitiesQuery();

  let classes = ["menu-city"];
  if (className) classes = classes.concat(className.split(" "));

  useEffect(() => {
    if (cookies.city) setCity(cookies.city);
  }, [cookies.city]);

  if (data) {
    return (
      <div className={classes.join(" ")}>
        <Dropdown onSelect={(eventKey) => setCookie("city", eventKey)}>
          <span>Ваш город:</span>
          <Dropdown.Toggle
            variant="success"
            id="city"
            as="a"
            style={{ cursor: "pointer" }}
          >
            {city || data[0]}
          </Dropdown.Toggle>

          <Dropdown.Menu>
            {data.map((item) => (
              <Dropdown.Item
                key={item}
                href="#"
                eventKey={item}
                active={city === item}
              >
                {item}
              </Dropdown.Item>
            ))}
          </Dropdown.Menu>
        </Dropdown>
        <div className="city-choose" style={city ? {} : { display: "flex" }}>
          <h5 className="w-100 mb-3">Ваш город {data[0]}?</h5>
          <button
            className="btn btn-primary btn-sm"
            onClick={() => setCookie("city", data[0])}
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
