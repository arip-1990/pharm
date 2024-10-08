import styles_ from './gallery.module.css';
import React, { useState } from "react";
import Modal from './Modal';
import Image from "next/image";
import {IPhotoKids} from '../../../models/IPhotoKids'
import {useAddLikeMutation, useFetchArrayIdPhotoQuery, useUploadPhotoMutation} from "../../../lib/kidsPhotoService";
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

export const Gallery: React.FC<IPhotoKids[] | any> = ({ photos, setAge, age }) => {

    const [addLike, {isLoading, error}] = useAddLikeMutation()
    const [isModalOpen, setModalOpen] = useState(false);
    const [selectedImage, setSelectedImage] = useState<{src: string, title: string, author: string, category: string} | null>(null);
    const [idCard, setIdCard] = useState<number | null>(null);
    const user = useAuth();
    const {data} = useFetchArrayIdPhotoQuery('ee374378-12eb-ed11-80cc-001dd8b75065')
    const [isOpen, setIsOpen] = useState(false);
    const [myPhoto, setMyPhoto] = useState(false);

    const category = () => {

    }

    const openModal = (src: string, title: string, author: string, category: string) => {
        setSelectedImage({ src, title, author, category });
        setModalOpen(true);
    };

    const closeModal = () => {
        setModalOpen(false);
        setSelectedImage(null);
    };


    const handleLike = async (photo_id: number) => {
        try {
            setIdCard(photo_id);
            await addLike({ photo_id }).unwrap();

        } catch (error: any) {
            console.log(error)
        }
    };



    const checkLike = (photo_id:number) => {
        if (!data) return false
        const ids = [];
        for (let i = 0; i < data.length; i++) {
            ids.push(data[i].id);
        }
        if (ids.includes(photo_id)) return true
    }
    //  вот тут не забудь добавить useAuth
    const filteredPhotos = myPhoto ? photos?.filter((photo: IPhotoKids) => photo.user_id === '70277a84-013c-ed11-80cb-001dd8b75065') : photos;

    return (
        <div className={styles_.container}>
            <h1 className={styles_.title}>ГАЛЕРЕЯ</h1>

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

                {'user.isAuth' ? myPhoto ?
                    <button className={styles_.categoryMyPhotosActive}
                       onClick={() => setMyPhoto(true)}>Мои рисунки</button>
                    :
                    <button className={styles_.categoryMyPhotosNotActive}
                            onClick={() => setMyPhoto(true)}>Мои рисунки</button>
                   : ''
                }

            </div>

            <div className={styles_.grid}>

                {filteredPhotos?.length !== 0 ? filteredPhotos?.map((photo: IPhotoKids) => {
                    return (
                        <div key={photo.id} className={styles_.card}>
                            <Image
                                src={photo.link}
                                width={300}
                                height={220}
                                alt={'sss'}
                                className={styles_.image}
                                onClick={() => openModal(
                                    photo.link,
                                    photo.photo_name,
                                    photo.first_name,
                                    photo.age_category.Age
                                )}
                            />

                            <div className={styles_.cardContent}>
                                <h5>{photo.photo_name}</h5>
                                <p>Автор: {photo.first_name} {photo.last_name}</p>
                                <p>Категория: {photo.age_category.Age} лет</p>
                                <h5 style={{color:"#c39020"}}><b>Количество голосов {photo.users_likes.length}</b></h5>

                                {checkLike(photo.id) ?
                                    <button
                                        className={styles_.voteButtonLikes}
                                        onClick={() => handleLike(photo.id)}
                                    >
                                        {photo.id == idCard && isLoading ? "Загрузка ..." : 'Проголосованно'}
                                    </button>
                                    :
                                    <button
                                        className={styles_.voteButton}
                                        onClick={() => handleLike(photo.id)}
                                    >
                                        {photo.id == idCard && isLoading ? "Загрузка ..." : 'Проголосовать'}
                                    </button>
                                }

                            </div>
                        </div>
                    )
                }): 'Фото отсутствует'}

            </div>
            {myPhoto ?
                <div>
                    <button className={styles_.downloadButton} onClick={() => setIsOpen(true)}>Загрузить фотографию</button>
                    <button className={styles_.downloadButton} onClick={() => setMyPhoto(false)}>покинуть мои рисунки</button>
                </div>
                :
                ''
            }
            {isOpen ?
                <FormModal open={setIsOpen}/> : ''
            }

            {/* Модальное окно */}
            {selectedImage && (
                <Modal
                    isOpen={isModalOpen}
                    onClose={closeModal}
                    imageSrc={selectedImage.src}
                    title={selectedImage.title}
                    author={selectedImage.author}
                    category={selectedImage.category}
                />
            )}
        </div>
    );
};
