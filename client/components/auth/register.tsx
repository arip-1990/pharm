import { useFormik } from 'formik';

interface PropsType {
    onSubmit: (values: {email: string, name: string, phone: string, password: string, rule: number}) => void
}
 
const Register = ({onSubmit}: PropsType) => {
    const formik = useFormik({
        initialValues: {email: '', name: '', phone: '', password: '', rule: 0},
        onSubmit: values => {
            console.log(values);
            onSubmit(values);
        },
    });

    return (
        <form onSubmit={formik.handleSubmit}>
            <div className="mb-3">
                <label htmlFor="email" className="form-label">Почта</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    className="form-control"
                    onChange={formik.handleChange}
                    value={formik.values.email}
                />
            </div>
            <div className="mb-3">
                <label htmlFor="name" className="form-label">ФИО</label>
                <input
                    id="name"
                    name="name"
                    type="text"
                    className="form-control"
                    onChange={formik.handleChange}
                    value={formik.values.name}
                />
            </div>
            <div className="mb-3">
                <label htmlFor="phone" className="form-label">Мобильный телефон</label>
                <input
                    id="phone"
                    name="phone"
                    type="text"
                    className="form-control"
                    onChange={formik.handleChange}
                    value={formik.values.phone}
                />
            </div>
            <div className="mb-3">
                <label htmlFor="password" className="form-label">Пароль</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    className="form-control"
                    onChange={formik.handleChange}
                    value={formik.values.password}
                />
            </div>
            <div className="form-check">
                <input className="form-check-input" type="checkbox" name='news' id="news" />
                <label className="form-check-label" htmlFor="news" style={{fontSize: '0.85rem'}}>
                    Да, я соглашаюсь получать новости и информацию об акциях
                </label>
            </div>
            <div className="form-check">
                <input
                    id="rule"
                    name='rule'
                    type="checkbox"
                    className="form-check-input"
                    onChange={formik.handleChange}
                    value={formik.values.rule}
                />
                <label className="form-check-label" htmlFor="rule" style={{fontSize: '0.85rem'}}>
                    Я согласен с правилами сайта
                </label>
            </div>
            <div className="row mt-3">
                <div className="col text-center">
                    <button type="submit" className="btn btn-primary">Зарегистрироваться</button>
                </div>
            </div>
        </form>
    );
};

export default Register;
