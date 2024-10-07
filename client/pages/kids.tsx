import React, {Component, useCallback, useState} from 'react';
import Breadcrumbs from "../components/breadcrumbs";
import Card from "../components/card";
import Pagination from "../components/pagination";
import Layout from "../templates";
import Image from "next/image";
import photo1 from "../assets/images/kids/1.png"
import photo2 from "../assets/images/kids/2.png"

import {Gallery} from "../components/Kids/Cards/Gallery";
import {Button} from "react-bootstrap";
import {useFetchCardsQuery} from "../lib/kidsPhotoService"
import Top from "../components/Kids/upperPart/Top";
const Kids = () => {
    const [age, setAge] = useState(1);
    const { data, } = useFetchCardsQuery(age);

    const getDefaultGenerator = useCallback(
        () => [{ href: "/kids", text: "Конкурс детского рисунка" }],
        []
    );


    return (
        <Layout title="Акции - Сеть аптек 120/80" description="Акции сайта.">
            <Breadcrumbs getDefaultGenerator={getDefaultGenerator}/>

            <Top />

            <Gallery photos={data} age={setAge}/>

        </Layout>
    );
};

export default Kids;