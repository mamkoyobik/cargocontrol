<footer id="footer" class="cc-footer">
    <div class="container">
        <div class="cc-footer-grid">
            <section class="cc-footer-col cc-footer-terminals">
                <?php include_once('terminals.php'); ?>
            </section>

            <section class="cc-footer-col cc-footer-nav-col">
                <ul class="cc-footer-nav">
                    <li><a href="#land">Главная</a></li>
                    <li><a href="#services">Услуги</a></li>
                    <li><a href="#gallery">Фотогалерея</a></li>
                    <li><a href="#contacts">Контакты</a></li>
                    <li><a href="#about">о нас</a></li>
                </ul>
                <button type="button" class="cc-footer-action btn-modal" data-toggle="modal" data-target="#contactForm_modal">Отправить заявку</button>
            </section>

            <section class="cc-footer-col cc-footer-contact">
                <h3>контакты</h3>
                <p class="contact_info">
                    <span><strong>Адрес:</strong> </span><br>198096, Россия, г. Санкт-Петербург,<br/> 3-й район морского порта, д.4, стр.1, пом. 5-Н<br>
                    <span><strong>Телефон:</strong> </span><a href="tel:+78129213074">+7 (812) 921-30-74</a><br>
                    <span><strong>E-mail:</strong> </span><a href="mailto:cargo@cargocontrol.ru">cargo@cargocontrol.ru</a><br>
                    <span><strong>Режим работы:</strong> </span><br>
                    Ежедневно, 24 часа!<br>
                </p>
            </section>
        </div>
    </div>
</footer>

<section id="copyright" class="cc-copyright">
    <div class="container">
        <p>&copy; Copyright 2015-<?php echo date("Y"); ?> CARGO CONTROL</p>
    </div>
</section>

<?php include_once('contact-form.php'); ?>
