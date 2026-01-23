import React, { FC } from "react";
import { Col, Row } from "react-bootstrap";
import { IBanner } from "../../models/IBanner";

import styles from "./Banner.module.scss";
import AdFoxBanner from "../addFoxBanner/AddFoxBanner";
import {XoaltBanner} from "../addFoxBanner/UpdSimbad";

interface PropsType {
  data: IBanner[];
}

const Extra: FC<PropsType> = ({ data }) => {
  return (
    <Row style={{ rowGap: "1rem" }}>
        <Col
          xs={12}
          sm={6}
          lg={12}
          style={{ textAlign: "center" }}
        >
            <div style={{height:"100%",  display:"flex", flexDirection:"column", justifyContent:"center"}}>
                <div style={{marginTop:"20px", marginBottom:"25px"}}>
                    {/*<AdFoxBanner*/}
                    {/*    containerId="adfox_174055455540329080"*/}
                    {/*    params={{p1: 'dghxh', p2: 'ixfw'}}*/}
                    {/*/>*/}
                    <XoaltBanner spotId={35517} />

                </div>
                <div>
                    {/*<AdFoxBanner*/}
                    {/*    containerId="adfox_174055458536479080"*/}
                    {/*    params={{p1: 'dghxi', p2: 'ixfw'}}*/}
                    {/*/>*/}
                    <XoaltBanner spotId={35518} />
                </div>
            </div>

        </Col>

      {/*{data*/}
      {/*  .filter((_, index) => index < 2)*/}
      {/*  .map((banner) => (*/}
      {/*    <Col*/}
      {/*      key={banner.id}*/}
      {/*      xs={12}*/}
      {/*      sm={6}*/}
      {/*      lg={12}*/}
      {/*      style={{ textAlign: "center" }}*/}
      {/*    >*/}
      {/*      {banner.link ? (*/}
      {/*        <a href={banner.link} target="_blank">*/}
      {/*          <img*/}
      {/*            className={styles.banner_extra}*/}
      {/*            src={banner.picture.main}*/}
      {/*          />*/}
      {/*        </a>*/}
      {/*      ) : (*/}
      {/*        <img className={styles.banner_extra} src={banner.picture.main} />*/}
      {/*      )}*/}
      {/*    </Col>*/}
      {/*  ))}*/}
    </Row>
  );
};

export { Extra };
