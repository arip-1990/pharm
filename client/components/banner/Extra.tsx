import { FC } from "react";
import { Col, Row } from "react-bootstrap";
import { IBanner } from "../../models/IBanner";

import styles from "./Banner.module.scss";

interface PropsType {
  data: IBanner[];
}

const Extra: FC<PropsType> = ({ data }) => {
  return (
    <Row style={{ rowGap: "1rem" }}>
      {data
        .filter((_, index) => index < 2)
        .map((banner) => (
          <Col
            key={banner.id}
            xs={12}
            sm={6}
            lg={12}
            style={{ textAlign: "center" }}
          >
            {banner.link ? (
              <a href={banner.link} target="_blank">
                <img
                  className={styles.banner_extra}
                  src={banner.picture.main}
                />
              </a>
            ) : (
              <img className={styles.banner_extra} src={banner.picture.main} />
            )}
          </Col>
        ))}
    </Row>
  );
};

export { Extra };
