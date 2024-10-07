import React, {useState} from 'react';
import Image from "next/image";
import photo1 from "../../../assets/images/kids/1.png";
import photo2 from "../../../assets/images/kids/2.png";
import style from "./top.module.css";
import categoryThree from "../../../assets/images/kids/Кнопка_от 3 до 5 белая.png"
import categorySix from "../../../assets/images/kids/Кнопка_от 6 до 8 белая.png"
import categoryNine from "../../../assets/images/kids/Кнопка_от 9 до 11 белая.png"
import categoryTwelve from "../../../assets/images/kids/Кнопка_от 12 до 14 белая.png"
import ParticipateYellow from "../../../assets/images/kids/Кнопка_участовать_желтый_фон.png"
import priz from "../../../assets/images/kids/Призы.png"
import voteStyle from "./vote.module.css"
// import hippopotamusVote from "../../../assets/images/kids/Бегемот_голосование.png"
import vote from "../../../assets/images/kids/Голосование.png"


import hippopotamusVote from "../../../assets/images/kids/бегемот с облаком.png"
const Top = () => {

    const [showMore, setShowMore] = useState(false);

    const texts = [
        '"At vero eos et accusamus et iusto odio"',
        '"At vero eos et accusamus et iusto odio"',
        '"At vero eos et accusamus et iusto odio"',
        '"At vero eos et accusamus et iusto odio"',
        '"At vero eos et accusamus et iusto odio"',
        '"At vero eos et accusamus et iusto odio"',
        '"At vero eos et accusamus et iusto odio"',
        '"At vero eos et accusamus et iusto odio"',
        '"At vero eos et accusamus et iusto odio"',
        '"At vero eos et accusamus et iusto odio"',
        '"At vero eos et accusamus et iusto odio"',
        '"At vero eos et accusamus et iusto odio"',
        '"At vero eos et accusamus et iusto odio"',
        '"At vero eos et accusamus et iusto odio"',
        '"At vero eos et accusamus et iusto odio"',
        '"At vero eos et accusamus et iusto odio"',
        '"At vero eos et accusamus et iusto odio"',
    ];

    const visibleTexts = showMore ? texts : texts.slice(0, 5);

    return (
        <div>

            <div className={style.firstImageContainer}>
                <div className={style.firstImage}>
                    <Image src={photo1} alt={"not found"}/>
                </div>
            </div>

            <div className={style.categories}>
                <div className={style.categoriesImg}>
                    <Image src={categoryThree}/>
                </div>
                <div className={style.categoriesImg}>
                    <Image src={categorySix}/>
                </div>
                <div className={style.categoriesImg}>
                    <Image src={categoryNine}/>
                </div>
                <div className={style.categoriesImg}>
                    <Image src={categoryTwelve}/>
                </div>
            </div>

            <div style={{display: "flex", justifyContent: "center"}}>
                <div className={style.ParticipateYellow}>
                    <Image src={ParticipateYellow}/>
                </div>
            </div>


            <div className={style.prizesContainer}>
                <div className={style.leftSide}>
                    <div>
                        <Image width={2000} height={1200} src={priz} alt="Призы"/>
                    </div>
                    <div className={style.text}>
                        {visibleTexts.map((text, index) => (
                            <p key={index}>{text}</p>
                        ))}
                    </div>
                    <button
                        style={{
                            backgroundColor: "#27bfc1",
                            color: "white",
                            border: "none",
                            borderRadius: "55px"
                        }}
                        onClick={() => setShowMore(!showMore)}
                    >
                        {showMore ? "Показать меньше" : "Показать больше"}
                    </button>
                </div>

                <div className={style.rightSide}>
                    <Image src={photo2} alt="photo2"/>
                </div>
            </div>


            {/*Голосование*/}

            <div className={voteStyle.votingContainer}>
                <div className={voteStyle.leftSide}>
                    <div>
                        <Image src={hippopotamusVote} alt="Бегемот" />
                    </div>
                    <div className={voteStyle.textSection}>
                        <p>Победители будут определены путём открытого голосования на сайте "Мой Любимый Дагестан" с 1
                            октября по 30 декабря 2024 года.</p>
                        <p>Принять участие может каждый зарегистрированный пользователь сайта. Для участия вам
                            необходимо зарегистрироваться, а затем отдать свой голос за понравившуюся работу.</p>
                        <p>Принять участие может каждый зарегистрированный пользователь сайта. Для участия вам
                            необходимо зарегистрироваться, а затем отдать свой голос за понравившуюся работу.</p>
                        <p>Принять участие может каждый зарегистрированный пользователь сайта. Для участия вам
                            необходимо зарегистрироваться, а затем отдать свой голос за понравившуюся работу.</p>

                    </div>
                </div>
                <div className={voteStyle.rightSide}>
                    <Image src={ParticipateYellow} className={voteStyle.participateButton}/>
                </div>
            </div>

        </div>
    );
};

export default Top;