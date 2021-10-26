<div class="modal fade" data-type="login" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Вход</h5>
                <button class="btn-close" data-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form name="login" method="post" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="login" class="form-label">Мобильный телефон или почта</label>
                        <input type="text" name="phone" class="form-control" id="login" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Пароль</label>
                        <input type="password" name="password" class="form-control" id="password" required>
                    </div>
                    <div class="row align-items-center">
                        <a class="col-7" href="{{ url('/') }}">Забыли пароль?</a>
                        <span class="col-5 text-end">
                            <button type="submit" class="btn btn-primary">Войти</button>
                        </span>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-center">
                <button class="btn btn-primary" data-toggle="modal" data-target="register">Зарегистрироваться</button>
            </div>
        </div>
    </div>
</div>
