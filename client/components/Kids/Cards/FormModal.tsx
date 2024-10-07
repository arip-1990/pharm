import React, {useState} from 'react';
import { Formik, Form, Field } from 'formik';
import { useUploadPhotoMutation } from '../../../lib/kidsPhotoService';
import styles from './formModal.module.css';

const FormModal = ({ open }: any) => {
    const [uploadPhoto] = useUploadPhotoMutation();
    const [fileName, setFileName] = useState('Файл не выбран');

    if (!open) return null;

    return (
        <div className={styles.modalOverlay}>
            <div className={styles.modal}>
                <div className={styles.modalHeader}>
                    <button className={styles.closeButton} onClick={() => open(false)}>
                        &times;
                    </button>
                </div>
                <Formik
                    initialValues={{
                        file: null,
                        photo_name: '',
                        birthdate: '',
                        first_name: '',
                        last_name: '',
                        middle_name: '',
                        user_id: '',
                    }}
                    onSubmit={async (values) => {
                        const formData = new FormData();
                        formData.append('file', values.file);
                        formData.append('photo_name', values.photo_name);
                        formData.append('birthdate', values.birthdate);
                        formData.append('first_name', values.first_name);
                        formData.append('last_name', values.last_name);
                        formData.append('middle_name', values.middle_name);
                        formData.append('user_id', 'ee374378-12eb-ed11-80cc-001dd8b75065');

                        try {
                            await uploadPhoto(formData).unwrap();
                            open(false); // Закрыть модальное окно после успешной загрузки
                        } catch (error) {
                            console.error('Ошибка при загрузке фото:', error);
                        }
                    }}
                >
                    {({ setFieldValue }) => (
                        <Form>
                            <div className={styles.customFileInput}>
                                <input
                                    id="file"
                                    name="file"
                                    type="file"
                                    onChange={(event) => {
                                        const file = event.currentTarget.files[0];
                                        setFieldValue('file', file);
                                        setFileName(file ? file.name : 'Файл не выбран');
                                    }}
                                />
                                <span className={styles.customFileLabel}>
                                    {fileName}
                                </span>
                            </div>

                            <div className={styles.formField}>
                                <Field
                                    id="photo_name"
                                    name="photo_name"
                                    placeholder=" "
                                    className={styles.formikField}
                                />
                                <label htmlFor="photo_name">Название фото</label>
                            </div>

                            <div className={styles.formField}>
                                <Field
                                    id="birthdate"
                                    name="birthdate"
                                    type="date"
                                    placeholder=" "
                                    className={styles.formikField}
                                />
                                <label htmlFor="birthdate">Дата рождения</label>
                            </div>

                            <div className={styles.formField}>
                                <Field
                                    id="first_name"
                                    name="first_name"
                                    placeholder=" "
                                    className={styles.formikField}
                                />
                                <label htmlFor="first_name">Имя</label>
                            </div>

                            <div className={styles.formField}>
                                <Field
                                    id="last_name"
                                    name="last_name"
                                    placeholder=" "
                                    className={styles.formikField}
                                />
                                <label htmlFor="last_name">Фамилия</label>
                            </div>

                            <div className={styles.formField}>
                                <Field
                                    id="middle_name"
                                    name="middle_name"
                                    placeholder=" "
                                    className={styles.formikField}
                                />
                                <label htmlFor="middle_name">Отчество</label>
                            </div>

                            <button type="submit" className={styles.submitButton}>
                                Загрузить фото
                            </button>
                        </Form>
                    )}
                </Formik>
            </div>
        </div>
    );
};

export default FormModal;
