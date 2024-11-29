import React, {Component, useCallback, useState} from 'react';
import Breadcrumbs from "../components/breadcrumbs";
import Card from "../components/card";
import Pagination from "../components/pagination";
import Layout from "../templates";
import Image from "next/image";
import photo1 from "../assets/images/kids/1.png"
import photo2 from "../assets/images/kids/2.png"

import {Gallery} from "../components/Kids/Cards/Gallery";
import {useFetchCardsQuery} from "../lib/kidsPhotoService"
import Top from "../components/Kids/upperPart/Top";

const Kids = () => {
    const [age, setAge] = useState(1);
    const { data} = useFetchCardsQuery(age);

    const getDefaultGenerator = useCallback(
        () => [{ href: "/kids", text: "Конкурс детского рисунка" }],
        []
    );


    return (
        <Layout title="Конкурс детского рисунка 120на80" description="Конкурс">
            
            <Breadcrumbs getDefaultGenerator={getDefaultGenerator}/>

            <Top />

            <Gallery photos={data || []} setAge={setAge} age={age} />

        </Layout>
    );
};

export default Kids;


