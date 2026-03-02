<?php
if (!isset($activePage)) {
    $activePage = '';
}
?>
<header id="header" class="cc-header">
    <div class="cc-header-main-wrap">
        <div class="container">
            <div class="cc-header-main">
                <a href="#land" class="navbar-brand" aria-label="CARGO CONTROL">
                    <img src="img/logo-new.png" alt="Независимый грузовой сюрвейер - контроль сохранности грузов" loading="eager" fetchpriority="high" decoding="async" width="481" height="65">
                </a>

                <button
                    id="toggle_mobile_menu"
                    type="button"
                    aria-label="Меню"
                    aria-controls="mainmenu_wrapper"
                    aria-expanded="false"
                >
                    <span></span>
                </button>

                <div id="mainmenu_wrapper">
                    <nav class="cc-header-nav" aria-label="Главное меню">
                        <ul id="mainmenu" class="nav sf-menu">
                            <li<?php echo $activePage === 'home' ? ' class="active"' : ''; ?>><a href="#land">Главная</a></li>
                            <li<?php echo $activePage === 'services' ? ' class="active"' : ''; ?>><a href="#services">Услуги</a></li>
                            <li<?php echo $activePage === 'gallery' ? ' class="active"' : ''; ?>><a href="#gallery">Фотогалерея</a></li>
                            <li<?php echo $activePage === 'contact' ? ' class="active"' : ''; ?>><a href="#contacts">Контакты</a></li>
                            <li<?php echo $activePage === 'about' ? ' class="active"' : ''; ?>><a href="#about">О нас</a></li>
                        </ul>
                    </nav>
                    <div class="cc-header-meta">
                        <a class="cc-header-phone" href="tel:+78129213074" data-cta="header_phone">+7 (812) 921-30-74</a>
                        <span class="cc-header-hours">Ежедневно, 24 часа!</span>
                        <button type="button" class="cc-header-action btn-modal" data-toggle="modal" data-target="#contactForm_modal" data-cta="header_request">Отправить заявку</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="cc-header-progress" aria-hidden="true">
        <span class="cc-header-progress-bar"></span>
    </div>
</header>
