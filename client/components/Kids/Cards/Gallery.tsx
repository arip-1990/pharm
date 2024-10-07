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
export const Gallery: React.FC<IPhotoKids[] | any> = ({ photos, age }) => {

    const [addLike, {isLoading, error}] = useAddLikeMutation()
    const [isModalOpen, setModalOpen] = useState(false);
    const [selectedImage, setSelectedImage] = useState<{src: string, title: string, author: string, category: string} | null>(null);
    const [idCard, setIdCard] = useState<number | null>(null);
    const { user } = useAuth();
    const {data} = useFetchArrayIdPhotoQuery('ee374378-12eb-ed11-80cc-001dd8b75065')
    const [isOpen, setIsOpen] = useState(false);

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


    return (
        <div className={styles_.container}>
            <h1 className={styles_.title}>ГАЛЕРЕЯ</h1>

            <div className={styles_.category}>

                <div className={styles_.categoryImg}>
                    <Image src={categoryThree} onClick={() => age(1)}/>
                </div>

                <div className={styles_.categoryImg}>
                    <Image src={categorySix} onClick={() => age(2)}/>
                </div>

                <div className={styles_.categoryImg}>
                    <Image src={categoryNine} onClick={() => age(3)}/>
                </div>

                <div className={styles_.categoryImg}>
                    <Image src={categoryTwelve} onClick={() => age(4)}/>
                </div>

                {/*<button className={styles_.ageButton} onClick={() => age(2)}>от 6 до 8</button>*/}
                {/*<button className={styles_.ageButton} onClick={() => age(3)}>от 9 до 11</button>*/}
                {/*<button className={styles_.ageButton} onClick={() => age(4)}>от 12 до 14</button>*/}
                <button className={styles_.downloadButton} onClick={() => setIsOpen(true)}>Загрузить фотографию</button>
            </div>

            <div className={styles_.grid}>

                {photos?.length != 0 ? photos?.map((photo: IPhotoKids) => {
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
                }): 'Фоток пока нет, Будьте первым'}

            </div>

            {isOpen ?
                <FormModal open={setIsOpen} />:''
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
