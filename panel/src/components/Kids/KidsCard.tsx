import React, { useState } from 'react';
import styles from './kidsCard.module.css';
import {useDeletePhotosMutation, useFetchBannersQuery, useUpdatePhotosMutation} from '../../services/KidsKonkurs';
import { DeleteTwoTone } from "@ant-design/icons";

const KidsCard = ({ published }: { published: boolean }) => {
  const { data: photos } = useFetchBannersQuery(published); // Получаем данные из запроса
  const [selectedPhotos, setSelectedPhotos] = useState<number[]>([]); // Состояние для выбранных фотографий
  const [isModalOpen, setIsModalOpen] = useState(false); // Состояние для модалки
  const [currentPhoto, setCurrentPhoto] = useState<string | null>(null); // Выбранное фото для модалки
  const [deletePhoto] = useDeletePhotosMutation();
  const [updatePhotos] = useUpdatePhotosMutation();

  // Функция для выбора всех фотографий
  const handleSelectAll = () => {
    if (selectedPhotos.length === photos?.length) {
      setSelectedPhotos([]); // Если все выбраны, снять выбор
    } else {
      setSelectedPhotos(photos?.map(photo => photo.id) || []); // Выбрать все
    }
  };

  // Функция для выбора одного фото
  const handlePhotoSelect = (id: number) => {
    if (selectedPhotos.includes(id)) {
      setSelectedPhotos(selectedPhotos.filter(photoId => photoId !== id)); // Снять выбор
    } else {
      setSelectedPhotos([...selectedPhotos, id]); // Добавить в выбранные
    }
  };

  // Открытие модалки с большим фото
  const openModal = (photoLink: string) => {
    setCurrentPhoto(photoLink);
    setIsModalOpen(true);
  };

  // Закрытие модалки
  const closeModal = () => {
    setCurrentPhoto(null);
    setIsModalOpen(false);
  };


  const handleDelete = async (id:number) => {
    try {
      // Вызов мутации для удаления фотографии по id
      await deletePhoto({ id }).unwrap();
      console.log('Фото успешно удалено');
    } catch (error) {
      console.error('Ошибка при удалении фотографии:', error);
    }
  };


  const handlePublish = async () => {
    if (selectedPhotos.length === 0) {
      alert('Пожалуйста, выберите хотя бы одно фото для публикации.');
      return;
    }
    console.log(typeof selectedPhotos[0], selectedPhotos[0])
    try {
      // Вызов мутации для обновления/публикации фотографий
      await updatePhotos({ ids: selectedPhotos }).unwrap();
      console.log('Фотографии успешно опубликованы');
       // Сбрасываем выбранные фотографии после публикации
    } catch (error) {
      console.error('Ошибка при публикации фотографий:', error);
      alert('Произошла ошибка при публикации фотографий.');
    }
  };

  if (!photos || photos.length === 0) {
    return <div className={styles.noPhotos}>Нет фотографий</div>;
  }

  return (
    <div>
      {/* Кнопка для выбора всех фотографий, если пользователь не активен */}
      {!published && (
        <>
          <button onClick={handleSelectAll} className={styles.selectAllButton}>
            {selectedPhotos.length === photos.length ? 'Снять выбор со всех' : 'Выбрать все'}
          </button>
          <button onClick={handlePublish} className={styles.selectAllButtonPublish}>
            Опубликовать
          </button>
        </>
      )}

      <div className={styles.gridContainer}>
        {photos.map((photo) => (
          <div key={photo.id} className={styles.photoCard}>
            <img
              src={photo.link}
              alt={`Photo ${photo.photo_name}`}
              className={styles.photoImage}
              onClick={() => openModal(photo.link)} // Открытие модалки при клике на фото
            />
            <div className={styles.photoFooter}>

                {!published ? (
                  <>
                    <input
                      type="checkbox"
                      id={`checkbox-${photo.id}`}
                      className={styles.photoLabel}
                      checked={selectedPhotos.includes(photo.id)}
                      onChange={() => handlePhotoSelect(photo.id)} // Выбор фото
                    />
                    <label htmlFor={`checkbox-${photo.id}`}>
                      {photo.photo_name}
                    </label>

                    <DeleteTwoTone onClick={() => handleDelete(photo.id)}/>

                  </>
                ) : (
                  // Если пользователь активен (published === true), отображаем кнопку удаления
                  <DeleteTwoTone onClick={() => handleDelete(photo.id)}/>
                )}

            </div>
          </div>
        ))}
      </div>

      {/* Модалка для увеличенного фото */}
      {isModalOpen && (
        <div className={styles.modal} onClick={closeModal}>
          <div className={styles.modalContent} onClick={(e) => e.stopPropagation()}>
            <span className={styles.closeButton} onClick={closeModal}>&times;</span>
            {currentPhoto && <img src={currentPhoto} alt="Большое фото" className={styles.largePhoto} />}
          </div>
        </div>
      )}
    </div>
  );
};

export default KidsCard;
