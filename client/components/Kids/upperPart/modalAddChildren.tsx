import React, { useState } from 'react';
import { Modal, Button, Form } from 'react-bootstrap';
import {useAddChildrenMutation} from "../../../lib/kidsPhotoService";

interface AddChildrenModalProps {
    show: boolean;
    handleClose: () => void;
}

const AddChildrenModal: React.FC<AddChildrenModalProps> = ({ show, handleClose }) => {
    const [childrenCount, setChildrenCount] = useState<number>(0);

    // RTK Query мутация
    const [addChildren] = useAddChildrenMutation()

    // Обработчик отправки формы
    const handleSave = async () => {
        try {
            await addChildren({ count: childrenCount }).unwrap();
            handleClose(); // Закрытие модалки при успешном добавлении
        } catch (err) {
            console.error('Ошибка при отправке данных:', err);
            alert('ошибка')
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
                        <Form.Label>Количество детей</Form.Label>
                        <Form.Label>Тут будут всякие условия и в конце обязаельно нужно что бы уазали коичесво детей</Form.Label>
                        <Form.Control
                            type="number"
                            value={childrenCount}
                            onChange={(e) => setChildrenCount(Number(e.target.value))}
                            min={0}
                            max={8}
                            placeholder="Введите количество детей"
                        />
                    </Form.Group>
                </Form>
            </Modal.Body>

            <Modal.Footer>
                <Button variant="secondary" onClick={handleClose}>
                    Отмена
                </Button>
                <Button
                    variant="primary"
                    onClick={handleSave}
                    // disabled={isLoading}
                >
                    Save
                    {/*{isLoading ? 'Сохранение...' : 'Сохранить'}*/}
                </Button>
            </Modal.Footer>
        </Modal>
    );
};

export default AddChildrenModal;
