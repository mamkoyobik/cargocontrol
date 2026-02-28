<!-- Modal -->
<div class="modal fade" id="contactForm_modal" tabindex="-1" role="dialog" aria-labelledby="contactFormLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                            class="sr-only">Закрыть</span></button>
                <h4 class="modal-title" id="contactFormLabel">Отправить заявку</h4>
            </div>
            <div class="modal-body">
                <form id="contactForm" action="handler.php" method="post" novalidate>
                    <div class="form-flex">
                        <div class="form-group">
                            <label for="name">Ваше имя*</label>
                            <input id="name" class="form-control" name="name" required type="text" placeholder="" autocomplete="name">
                        </div>
                        <div class="form-group">
                            <label for="company">Ваша компания</label>
                            <input id="company" class="form-control" name="company" type="text" placeholder="" autocomplete="organization">
                        </div>
                        <div class="form-group">
                            <label for="phone">Ваш телефон*</label>
                            <input id="phone" class="form-control" name="phone" required type="text" placeholder="" autocomplete="tel">
                        </div>
                        <div class="form-group">
                            <label for="email">Ваш e-mail*</label>
                            <input id="email" class="form-control" name="email" required type="email" placeholder="" autocomplete="email">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="message">Краткое описание необходимых работ:</label>
                        <textarea id="message" class="form-control" name="message" rows="3"></textarea>
                    </div>
                    <!-- Капча -->
                    <div class="form-group">
                        <div class="g-recaptcha" data-sitekey="6LeOD3IUAAAAAE-rVdZjMqniYBl0P1fnrbNA-5Lp"></div>
                    </div>

                    <div class="form-group">

                    <div class="required-text"><i>*Обязательные поля</i></div>
                    <button id="button" class="btn btn-primary btn-lg btn-block" type="submit" onclick="if (typeof yaCounter38639640 !== 'undefined') { yaCounter38639640.reachGoal('form'); } return true;">Отправить</button>
                    <div class="result">
                        <div id="answer"></div>
                        <div id="loader"><img src="img/preloader.gif" alt=""></div>
                    </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
