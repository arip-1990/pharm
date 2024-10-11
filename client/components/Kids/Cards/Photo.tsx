import React, {FC, MouseEvent, useState} from 'react';
import styles_ from "./gallery.module.css";
import Image from "next/image";
import {IPhotoKids} from "../../../models/IPhotoKids";
import {useAddLikeMutation, useFetchArrayIdPhotoQuery} from "../../../lib/kidsPhotoService";
// import Modal from "./Modal";
import Auth from "../../auth";
import {useAuth} from "../../../hooks/useAuth";
import Modal from 'react-bootstrap/Modal';
import styles from "./modal.module.css";

interface IPhotoProps {
    photo: IPhotoKids;
}

const Photo: FC<IPhotoProps> = ({photo}) => {
    const {isAuth} = useAuth();
    const [addLike, {isLoading, error}] = useAddLikeMutation();
    const [idCard, setIdCard] = useState<number | null>(null);
    const {data} = useFetchArrayIdPhotoQuery();
    const [isModalOpen, setModalOpen] = useState(false);
    const [selectedImage, setSelectedImage] = useState<{
        src: string,
        title: string,
        author: string,
        category: string
    } | null>(null);
    const [openAuthModal, setOpenAuthModal] = useState(false);

    const handleAuthLike = (e: MouseEvent) => {
        e.preventDefault();
        isAuth ? handleLike(photo.id) : setOpenAuthModal(true);
    };

    const closeModal = () => {
        setModalOpen(false);
        setSelectedImage(null);
    };

    const openModal = (src: string, title: string, author: string, category: string) => {
        setSelectedImage({ src, title, author, category });
        setModalOpen(true);
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
        <div key={photo.id} className={styles_.card}>
            <Image
                src={photo.link}
                width={300}
                height={220}
                alt='sss'
                className={styles_.image}
                onClick={() => openModal(
                    photo.link,
                    photo.photo_name,
                    photo.first_name,
                    photo.age_category?.Age
                )}
            />

            <div className={styles_.cardContent}>
                <h5>{photo.photo_name}</h5>
                <p>Автор: {photo.first_name} {photo.last_name}</p>
                <p>Категория: {photo.age_category?.Age} лет</p>
                <h5 style={{color: "#c39020"}}><b>Количество голосов {photo.user_likes_count}</b></h5>

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
                        onClick={handleAuthLike}
                    >
                        {photo.id == idCard && isLoading ? "Загрузка ..." : 'Проголосовать'}
                    </button>
                }

            </div>

            {/* Модальное окно */}
            {selectedImage && (
                // <Modal
                //     isOpen={isModalOpen}
                //     onClose={closeModal}
                //     imageSrc={selectedImage.src}
                //     title={selectedImage.title}
                //     author={selectedImage.author}
                //     category={selectedImage.category}
                // />
                <Modal show={isModalOpen} onHide={closeModal}>
                    <Modal.Header closeButton />
                    <Modal.Body>
                        <Image src={selectedImage.src} alt={selectedImage.title} width={640} height={640} objectFit="contain" />
                        <h3>{selectedImage.title}</h3>
                        <p>Автор: {selectedImage.author}</p>
                        <p>Категория: {selectedImage.category}</p>
                    </Modal.Body>
                </Modal>
            )}

            <Auth show={openAuthModal} onHide={() => setOpenAuthModal(false)}/>
        </div>
    )
}

export {Photo};
