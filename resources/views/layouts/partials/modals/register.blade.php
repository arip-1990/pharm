<div class="modal fade" data-type="register" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Регистрация</h5>
                <button class="btn-close" data-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form name="register" method="post" action="{{ route('register') }}">
                    <div class="mb-3">
                        <label for="email" class="form-label">Почта</label>
                        <input type="email" name="email" class="form-control" id="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">ФИО</label>
                        <input type="text" name="name" class="form-control" id="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Мобильный телефон</label>
                        <input type="text" name="phone" class="form-control" id="phone" required>
                    </div>
                    <div class="mb-3">
                        <label for="newPassword" class="form-label">Пароль</label>
                        <input type="password" name="password" class="form-control" id="newPassword" required>
                    </div>
                    <div class="mb-3">
                        <label for="passwordConfirm" class="form-label">Повторите пароль</label>
                        <input type="password" name="password_confirmation" class="form-control" id="passwordConfirm" required>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="news">
                        <label class="form-check-label" for="news">
                            Да, я соглашаюсь получать новости и информацию об акциях
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="rule" required>
                        <label class="form-check-label" for="rule">
                            Я согласен с правилами сайта
                        </label>
                    </div>
                    <div class="row mt-3">
                        <div class="col text-center">
                            <button class="btn btn-primary">Зарегистрироваться</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-center">
                <button class="btn btn-primary" data-toggle="modal" data-target="login">Войти</button>
            </div>
        </div>
    </div>
</div>
