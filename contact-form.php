<?php
require_once __DIR__ . '/session-bootstrap.php';

$formToken = $_SESSION['cc_form_token'] ?? '';
if (!is_string($formToken) || strlen($formToken) < 32) {
    if (function_exists('random_bytes')) {
        $formToken = bin2hex(random_bytes(32));
    } elseif (function_exists('openssl_random_pseudo_bytes')) {
        $formToken = bin2hex(openssl_random_pseudo_bytes(32));
    } else {
        $formToken = sha1(uniqid((string)mt_rand(), true));
    }
    $_SESSION['cc_form_token'] = $formToken;
}

$recaptchaSiteKey = trim((string)getenv('RECAPTCHA_SITE_KEY'));
$isRecaptchaConfigured = $recaptchaSiteKey !== '';
?>
<!-- Modal -->
<div
    class="modal fade"
    id="contactForm_modal"
    tabindex="-1"
    role="dialog"
    aria-modal="true"
    aria-labelledby="contactFormLabel"
    aria-hidden="true"
>
    <div class="modal-dialog cc-modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Закрыть</span>
                </button>
                <h4 class="modal-title" id="contactFormLabel">Отправить заявку</h4>
            </div>
            <div class="modal-body">
                <form id="contactForm" action="handler.php" method="post" novalidate>
                    <input type="text" name="website" class="cc-hp-field" tabindex="-1" autocomplete="off" aria-hidden="true">
                    <input type="hidden" name="_token" value="<?php echo htmlspecialchars($formToken, ENT_QUOTES, 'UTF-8'); ?>">

                    <div class="form-flex">
                        <div class="form-group">
                            <label for="name">Ваше имя*</label>
                            <input id="name" class="form-control" name="name" required type="text" maxlength="120" placeholder="" autocomplete="name">
                        </div>
                        <div class="form-group">
                            <label for="company">Ваша компания</label>
                            <input id="company" class="form-control" name="company" type="text" maxlength="160" placeholder="" autocomplete="organization">
                        </div>
                        <div class="form-group">
                            <label for="phone">Ваш телефон*</label>
                            <input id="phone" class="form-control" name="phone" required type="tel" maxlength="60" placeholder="" autocomplete="tel" inputmode="tel">
                        </div>
                        <div class="form-group">
                            <label for="email">Ваш e-mail*</label>
                            <input id="email" class="form-control" name="email" required type="email" maxlength="160" placeholder="" autocomplete="email">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="message">Краткое описание необходимых работ:</label>
                        <textarea id="message" class="form-control" name="message" rows="3" maxlength="3000"></textarea>
                    </div>

                    <div class="form-group">
                        <?php if ($isRecaptchaConfigured) { ?>
                            <div class="g-recaptcha" data-sitekey="<?php echo htmlspecialchars($recaptchaSiteKey, ENT_QUOTES, 'UTF-8'); ?>"></div>
                        <?php } else { ?>
                            <p class="cc-form-warning" role="status">Форма временно недоступна. Проверка ReCaptcha не настроена.</p>
                        <?php } ?>
                    </div>

                    <div class="form-group">
                        <div class="required-text"><i>*Обязательные поля</i></div>
                        <p class="cc-form-meta">Нажимая «Отправить», вы соглашаетесь на обработку контактных данных для ответа на заявку.</p>
                        <button
                            id="button"
                            class="btn btn-primary btn-lg btn-block"
                            type="submit"
                            data-loading-text="Отправляем..."
                            aria-controls="answer"
                            <?php echo $isRecaptchaConfigured ? '' : 'disabled aria-disabled="true"'; ?>
                        >Отправить</button>
                        <div class="result">
                            <div id="answer" aria-live="polite" aria-atomic="true"></div>
                            <div id="loader" aria-hidden="true"><img src="img/preloader.gif" alt=""></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
