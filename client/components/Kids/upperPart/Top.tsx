import React, {MouseEvent, useState} from 'react';
import Image from "next/image";
import end_1 from "../../../assets/images/kids/end_1.jpeg"
import end_test from "../../../assets/images/kids/END_CONCURSE.png"

import style from "./top.module.css";
import categoryThree from "../../../assets/images/kids/Кнопка_от 3 до 5 белая.png"
import categorySix from "../../../assets/images/kids/Кнопка_от 6 до 8 белая.png"
import categoryNine from "../../../assets/images/kids/Кнопка_от 9 до 11 белая.png"
import categoryTwelve from "../../../assets/images/kids/Кнопка_от 12 до 14 белая.png"
// import ParticipateYellow from "../../../assets/images/kids/Кнопка_участовать_желтый_фон.png"
import ParticipateYellow from "../../../assets/images/kids/Кнопка_участовать_красная_желтый_фон.png"
import priz from "../../../assets/images/kids/Призы.png"
import voteStyle from "./vote.module.css"
// import hippopotamusVote from "../../../assets/images/kids/Бегемот_голосование.png"
import vote from "../../../assets/images/kids/Голосование.png"
import FormModal from "../Cards/FormModal";

import hippopotamusVote from "../../../assets/images/kids/бегемот с облаком.png"
import {useAuth} from "../../../hooks/useAuth";
import AddChildrenModal from "./modalAddChildren";
import Auth from "../../auth";
const Top = () => {
    const [showModal, setShowModal] = useState(false);

    const handleOpen = () => setShowModal(true);
    const handleClose = () => setShowModal(false);

    const [showMore, setShowMore] = useState(false);
    const [openModal, setOpenModal] = useState(false);

    const auth = useAuth()

    const texts = [
        '1. 2000 ББ на карты лояльности',
        '2. Рюкзак  и зубная паста ТМ Лава Лава от блогера Влада А4',
        '3. Электрическая Зубная  щетка ТМ CS Medica',
        '4. Витамины для детей ТМ  Gummies Maxler',
        '5. Детская косметика  ТМ ЛА КРИ (гель и шампунь)',
        '6. Витамины для детей ТМ GLS',
        '7. БАД ТМ  Бифицин Бэйби',
        '8. Набор пластырей детских – 2 уп.',
        '9. БАД Омевит кидс',
        '10. БАД Флорбиолакт+ Флораброн сироп',
        '11. Книга - раскраска  Доктор Иммуно (+наклейки)',
        '12. Витамины для детей ТМ Барсукор («Мультивитамины» для детей и взрослых без ароматизатора №10, барсучий жир с витамином Д3 капсулы, 0,2 г, №100, таблетки с лизоцимом при дискомфорте в горле №30)',
        '13. Леденцы Чупа-Флю №10',
        '14. Помада гигиеническая',
        '15. Карандаши цветные',
        '16. Набор сладостей',
    ];

    const visibleTexts = showMore ? texts : texts.slice(0, 5);
    const handleSignIn = (e: MouseEvent) => {
        e.preventDefault();
        setOpenModal(true);
    };
    return (
        <div>

            <div className={style.firstImageContainer}>
                <div className={style.firstImage}>
                    <Image src={end_1} alt={"not found"}/>
                </div>
            </div>

            {/*<div className={style.categories}>*/}
            {/*    <div className={style.categoriesImg}>*/}
            {/*        <Image src={categoryThree}/>*/}
            {/*    </div>*/}
            {/*    <div className={style.categoriesImg}>*/}
            {/*        <Image src={categorySix}/>*/}
            {/*    </div>*/}
            {/*    <div className={style.categoriesImg}>*/}
            {/*        <Image src={categoryNine}/>*/}
            {/*    </div>*/}
            {/*    <div className={style.categoriesImg}>*/}
            {/*        <Image src={categoryTwelve}/>*/}
            {/*    </div>*/}
            {/*</div>*/}

            {/*<div style={{display: "flex", justifyContent: "center"}}>*/}
            {/*    <div className={style.ParticipateYellow} >*/}
            {/*        {auth.isAuth ?*/}
            {/*            <>*/}
            {/*                <Image src={ParticipateYellow} onClick={handleOpen}/>*/}
            {/*            </>*/}
            {/*            :*/}
            {/*            <>*/}
            {/*                <a href="#" onClick={handleSignIn}>*/}
            {/*                    <Image src={ParticipateYellow}/>*/}
            {/*                </a>*/}
            {/*            </>*/}
            {/*        }*/}
            {/*    </div>*/}
            {/*</div>*/}


            {/*<div className={style.prizesContainer}>*/}
            {/*<div className={style.leftSide}>*/}
            {/*        <div>*/}
            {/*            <Image width={2000} height={1200} src={priz} alt="Призы"/>*/}
            {/*        </div>*/}
            {/*        <div className={style.text}>*/}
            {/*            {visibleTexts.map((text, index) => (*/}
            {/*                <p key={index}>{text}</p>*/}
            {/*            ))}*/}
            {/*        </div>*/}
            {/*        <button*/}
            {/*            style={{*/}
            {/*                backgroundColor: "#27bfc1",*/}
            {/*                color: "white",*/}
            {/*                border: "none",*/}
            {/*                borderRadius: "55px"*/}
            {/*            }}*/}
            {/*            onClick={() => setShowMore(!showMore)}*/}
            {/*        >*/}
            {/*            {showMore ? "Показать меньше" : "Показать больше"}*/}
            {/*        </button>*/}
            {/*    </div>*/}

            {/*    <div className={style.rightSide}>*/}
            {/*        <Image src={photo2} alt="photo2"/>*/}
            {/*    </div>*/}
            {/*</div>*/}


            {/*Голосование/конец глосования*/}

            <div className={voteStyle.votingContainer}>
                <div className={voteStyle.leftSide}>
                    <div>
                        <Image src={end_test} alt="Бегемот"/>
                    </div>
                    <div className={voteStyle.textSection}>
                        <p>30 ноября 2024 года завершился конкурс детского рисунка «Мой Любимый Дагестан».
                        </p>
                        <p>После подведения итогов мы  поздравили победителей и вручили им памятные подарки. </p>
                        <p>Мы благодарны всем, кто принял участие в нашем конкурсе.</p>
                    </div>
                </div>
                {/*<div className={voteStyle.rightSide}>*/}
                {/*    { auth.isAuth ?*/}
                {/*        <>*/}
                {/*        <Image src={ParticipateYellow} onClick={handleOpen}/>*/}
                {/*        </>*/}
                {/*        :*/}
                {/*        <>*/}
                {/*            <a href="#" onClick={handleSignIn}>*/}
                {/*                <Image src={ParticipateYellow}/>*/}
                {/*            </a>*/}
                {/*        </>*/}
                {/*    }*/}
                {/*</div>*/}
            </div>
            <AddChildrenModal show={showModal} handleClose={handleClose} />
            {/*{openModal ? <FormModal open={setOpenModal}/> : ''}*/}
            <Auth show={openModal} onHide={() => setOpenModal(false)} />
        </div>
    );
};

export default Top;
