import React, { useState } from 'react';
import { Modal, Button, Form } from 'react-bootstrap';
import {useAddChildrenMutation, useFetchArrayChildrenCountQuery} from "../../../lib/kidsPhotoService";

interface AddChildrenModalProps {
    show: boolean;
    handleClose: () => void;
}

const AddChildrenModal: React.FC<AddChildrenModalProps> = ({ show, handleClose }) => {
    const [childrenCount, setChildrenCount] = useState<number>(0);

    // RTK Query мутация
    const [addChildren,] = useAddChildrenMutation()
    const {data:children} = useFetchArrayChildrenCountQuery()

    // Обработчик отправки формы
    const handleSave = async () => {
        try {
            await addChildren({ count: childrenCount }).unwrap();
            handleClose(); // Закрытие модалки при успешном добавлении
        } catch (err) {
            console.error('Ошибка при отправке данных:', err);
        }
    };

    return (
        <Modal show={show} onHide={handleClose}>
            <Modal.Header closeButton>
                <Modal.Title>Условия конкурса</Modal.Title>
            </Modal.Header>

            <Modal.Body>
                <Form>
                    <Form.Group controlId="childrenCount">

                        {children ?
                            <div>
                                <p>Что бы загрузить фото зайдите в раздел "мои рисунки" </p>
                            </div>
                            :
                            <>
                                <Form.Label>Добро пожаловать !</Form.Label>
                                <Form.Label>Для участия необходимо указать количество ваших детей</Form.Label>
                                <Form.Label>Учавствовать может каждый ребенок, необходимо загрузить фотографии ребенка</Form.Label>
                                <Form.Label>Каждый ребенок может загрузить по одной фотографии</Form.Label>
                                <Form.Label>Желаем успехов !</Form.Label>
                                <p></p>
                                <Form.Label>Укажите количество детей от 1 до 8</Form.Label>
                                <Form.Control
                                    type="number"
                                    value={childrenCount}
                                    onChange={(e) => setChildrenCount(Number(e.target.value))}
                                    min={0}
                                    max={8}
                                    placeholder="Введите количество детей"
                                />
                            </>
                            }
                    </Form.Group>
                </Form>
            </Modal.Body>

            <Modal.Footer>
                <Button variant="secondary" onClick={handleClose}>
                    Отмена
                </Button>
                {children ? '' :<Button
                    variant="primary"
                    onClick={handleSave}
                    // disabled={isLoading}
                    disabled={childrenCount > 8}
                >
                    Сохранить
                    {/*{isLoading ? 'Сохранение...' : 'Сохранить'}*/}
                </Button>}
            </Modal.Footer>
        </Modal>
    );
};

export default AddChildrenModal;
