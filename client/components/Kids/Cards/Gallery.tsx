import styles_ from './gallery.module.css';
import React, {useEffect, useState} from "react";
import Image from "next/image";
import {IPhotoKids} from '../../../models/IPhotoKids'
import { useFetchArrayUserChildrenPhotosQuery } from "../../../lib/kidsPhotoService";
import {useAuth} from "../../../hooks/useAuth";
import FormModal from "./FormModal"
import categoryThree from "../../../assets/images/kids/Кнопка_от 3 до 5 белая.png"
import categorySix from "../../../assets/images/kids/Кнопка_от 6 до 8 белая.png"
import categoryNine from "../../../assets/images/kids/Кнопка_от 9 до 11 белая.png"
import categoryTwelve from "../../../assets/images/kids/Кнопка_от 12 до 14 белая.png"

import categoryThreeColor from "../../../assets/images/kids/Кнопка_от 3 до 5 цвет.png"
import categorySixColor from "../../../assets/images/kids/Кнопка_от 6 до 8 цвет.png"
import categoryNineColor from "../../../assets/images/kids/Кнопка_от 9 до 11 цвет.png"
import categoryTwelveColor from "../../../assets/images/kids/Кнопка_от 12 до 14 цвет.png"
import AddChildrenModal from "../upperPart/modalAddChildren";
import {Button} from "react-bootstrap";
import {Photo} from "./Photo";

interface IPropsKids {
    photos: IPhotoKids[];
    age: number;
    setAge: (age: number) => void;
}

export const Gallery: React.FC<IPropsKids> = ({ photos, setAge, age }) => {
    const [showModal, setShowModal] = useState(false);
    const {user} = useAuth();
    const [isOpen, setIsOpen] = useState(false);
    const [myPhoto, setMyPhoto] = useState(false);
    const [filteredPhotos, setFilteredPhotos] = useState<IPhotoKids[]>(photos);
    const {data: userChildrenPhotos, isLoading:loadingCountPhoto} = useFetchArrayUserChildrenPhotosQuery()

    useEffect(() => {
        if (myPhoto) setFilteredPhotos(photos?.filter((photo: IPhotoKids) => photo.user_id == user.id));
        else setFilteredPhotos(photos);
    }, [photos, myPhoto]);

    const howOpenModal = () => {
        user?.childrenCount ? setIsOpen(true) : setShowModal(true);
    }

    const checked = () => {
        return user?.childrenCount <= userChildrenPhotos?.length
    }

    const cals = () => user?.childrenCount ? user?.childrenCount - userChildrenPhotos?.length : 0;

    return (
        <div className={styles_.container}>
            <h1 className={styles_.title}>ГАЛЕРЕЯ</h1>
            <h6>Выберите возрастную категорию</h6>

            <div className={styles_.category}>

                <div className={styles_.categoryImg}>
                    {age == 1 ? <Image src={categoryThreeColor} onClick={() => setAge(1)}/> :
                        <Image src={categoryThree} onClick={() => setAge(1)}/> }
                </div>

                <div className={styles_.categoryImg}>
                    {age == 2 ? <Image src={categorySixColor} onClick={() => setAge(2)}/> :
                        <Image src={categorySix} onClick={() => setAge(2)}/> }
                </div>

                <div className={styles_.categoryImg}>
                    {age == 3 ? <Image src={categoryNineColor} onClick={() => setAge(3)}/> :
                        <Image src={categoryNine} onClick={() => setAge(3)}/> }
                </div>

                <div className={styles_.categoryImg}>
                    {age == 4 ? <Image src={categoryTwelveColor} onClick={() => setAge(4)}/>:
                        <Image src={categoryTwelve} onClick={() => setAge(4)}/>}
                </div>

                {user ? myPhoto ?
                    <button className={styles_.categoryMyPhotosActive}
                       onClick={() => setMyPhoto(false)}>Мои рисунки</button>
                    :
                    <button className={styles_.categoryMyPhotosNotActive}
                            onClick={() => setMyPhoto(true)}>Мои рисунки</button>
                   : ''
                }

            </div>

            <div className={styles_.grid}>

                {filteredPhotos.length ? filteredPhotos.map((photo, index) => <Photo index={index} photo={photo} />) : 'Фото отсутствует'}

            </div>
            {myPhoto ?
                <>
                    {/* <div>
                        {checked() ? <h5>достигнут лимит рисунков</h5> :
                        <h5>вы можете загрузить еще {cals()} рис.</h5>}
                        <h3>Фото будет опубликовано на сайт в течение двух рабочих дней</h3>
                    </div> */}
                    <div style={{display: "flex", justifyContent: "center"}}>

                        <Button
                            onClick={howOpenModal}
                            disabled={true}
                            style={{marginTop: '20px'}}
                        >
                            Загрузить фотографию
                        </Button>

                        <div style={{marginRight: '10px', marginLeft: '10px'}}></div>

                        <Button
                            onClick={() => setMyPhoto(false)}
                            style={{marginTop: '20px'}}
                        >
                            Выйти из "мои рисунки"
                        </Button>
                    </div>
                </>
                :
                ''
            }
            {isOpen && user?.childrenCount ?
                <FormModal open={setIsOpen}/> : <AddChildrenModal show={showModal} handleClose={() => setShowModal(false)} />
            }
        </div>
    );
};

