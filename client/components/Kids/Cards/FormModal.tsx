import React, {useState} from 'react';
import { Formik, Form, Field } from 'formik';
import { useUploadPhotoMutation } from '../../../lib/kidsPhotoService';
import styles from './formModal.module.css';
import {useAuth} from "../../../hooks/useAuth";

const FormModal = ({ open }: any) => {
    const [uploadPhoto] = useUploadPhotoMutation();
    const [fileName, setFileName] = useState('Файл не выбран, максимальный размер файла 15мб');
    const {user} = useAuth();

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
                        formData.append('user_id', user.id);

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
                                    required={true}
                                    onChange={(event) => {
                                        const file = event.currentTarget.files[0];
                                        setFieldValue('file', file);
                                        setFileName(file ? file.name : 'Файл не выбран, максимальный размер файла 15мб');
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
                                    required={true}
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
                                    required={true}
                                />
                                <label htmlFor="birthdate">Дата рождения</label>
                            </div>

                            <div className={styles.formField}>
                                <Field
                                    id="first_name"
                                    name="first_name"
                                    placeholder=" "
                                    className={styles.formikField}
                                    required={true}
                                />
                                <label htmlFor="first_name">Имя</label>
                            </div>

                            <div className={styles.formField}>
                                <Field
                                    id="last_name"
                                    name="last_name"
                                    placeholder=" "
                                    className={styles.formikField}
                                    required={true}
                                />
                                <label htmlFor="last_name">Фамилия</label>
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
