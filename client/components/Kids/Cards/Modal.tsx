import React from 'react';
import styles from './modal.module.css'; // Создайте стили для модального окна

interface ModalProps {
    isOpen: boolean;
    onClose: () => void;
    imageSrc: string;
    title: string;
    author: string;
    category: string;
}

const Modal: React.FC<ModalProps> = ({ isOpen, onClose, imageSrc, title, author, category }) => {
    if (!isOpen) return null;

    return (
        <div className={styles.overlay} onClick={onClose}>
            <div className={styles.modal} onClick={(e) => e.stopPropagation()}>
                <button className={styles.closeButton} onClick={onClose}>✖</button>
                <img
                    src={imageSrc}
                    alt={title}
                    className={styles.modalImage}
                />
                <h3>{title}</h3>
                <p>Автор: {author}</p>
                <p>Категория: {category}</p>
            </div>
        </div>
    );
};

export default Modal;
