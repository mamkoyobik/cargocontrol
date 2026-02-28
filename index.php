<?php
$activePage = 'home';
$seoTitle = 'Грузовой сюрвейер в Санкт-Петербурге - CARGO CONTROL';
$seoDescription = 'Независимый грузовой сюрвейер CARGO CONTROL: инспекции грузов в портах, на терминалах и складах СПб и ЛО, фотоотчет, контроль погрузки и защита от убытков. Работаем 24 часа ежедневно.';
$canonicalUrl = 'https://cargocontrol.ru/';
$seoImage = 'https://cargocontrol.ru/img/background.jpg';
$seoImageWidth = 2560;
$seoImageHeight = 630;
?>
<!DOCTYPE html>
<!--[if lt IE 7]>
<html lang="ru" class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>
<html lang="ru" class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>
<html lang="ru" class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html lang="ru" class="no-js"> <!--<![endif]-->
<head>
    <?php include_once('head.php'); ?>
</head>
<body>
<a class="cc-skip-link" href="#services">Перейти к основному содержанию</a>

<div id="box_wrapper">
    <?php include_once('site-header.php'); ?>

    <section id="land" class="cc-hero" aria-labelledby="hero-heading">
        <div class="container">
            <div class="cc-hero-inner">
                <h1 id="hero-heading" class="sr-only">Независимый грузовой сюрвейер CARGO CONTROL в Санкт-Петербурге</h1>
                <img
                    src="img/logo-white.png"
                    alt="Независимый грузовой сюрвейер - контроль сохранности грузов"
                    width="949"
                    height="133"
                    fetchpriority="high"
                    decoding="async"
                >
                <div class="cc-hero-actions">
                    <a href="#services" class="cc-btn cc-btn-secondary">Услуги</a>
                    <button type="button" class="cc-btn cc-btn-primary btn-modal" data-toggle="modal" data-target="#contactForm_modal">Отправить заявку</button>
                </div>
                <ul class="cc-hero-facts" aria-label="Ключевая информация">
                    <li>Более 15 лет</li>
                    <li>Ежедневно, 24 часа</li>
                    <li>Санкт-Петербург, Ленинградская область, Москва и регионы РФ</li>
                </ul>
            </div>
        </div>
    </section>

    <main id="main-content">
        <section class="cc-story-section cc-section" aria-labelledby="story-heading">
            <div class="container">
                <header class="section_header cc-story-header">
                    <h2 id="story-heading" class="cc-story-title">УВЕРЕННОСТЬ в сохранности груза</h2>
                </header>

                <div class="cc-story-grid">
                    <article class="cc-story-item">
                        <div class="cc-story-icon"><img src="img/logo-new2.png" alt="" loading="lazy" decoding="async"></div>
                        <div class="cc-story-content">
                            <p class="cc-story-text"><strong>Вы экономите деньги, время и нервы.</strong> Мы работаем в сфере сюрвейерских услуг более 15 лет, и с нами Вы можете быть уверены в высоком качестве предоставляемых услуг, не опасаясь некомпетентных действий, мы всегда находим решения в самых сложных ситуациях, специализируемся в обработке самых сложных грузов! Успешно работаем с различными грузами на складах, железнодорожных станциях и терминалах города Санкт-Петербурга и Ленинградской области, города Москва и Московской области, и других регионах. Мы досконально изучили все процессы и знаем нюансы, которые помогут избежать непредвиденных ситуаций.</p>
                        </div>
                    </article>

                    <article class="cc-story-item">
                        <div class="cc-story-icon"><img src="img/logo-new2.png" alt="" loading="lazy" decoding="async"></div>
                        <div class="cc-story-content">
                            <p class="cc-story-text"><strong>Делаем работу быстро и качественно.</strong> Наша команда – это опытные профессионалы. Мы отлично знаем свою работу, поэтому всегда соблюдаем сроки и гарантируем максимальный контроль и сохранность грузов на любом этапе проводимых инспекций. Оперативность и четкость действий - это важная основа для решения поставленных задач.</p>
                        </div>
                    </article>

                    <article class="cc-story-item">
                        <div class="cc-story-icon"><img src="img/logo-new2.png" alt="" loading="lazy" decoding="async"></div>
                        <div class="cc-story-content">
                            <p class="cc-story-text"><strong>Объективная и независимая оценка – Вы всегда под защитой.</strong> Мы выступаем как третья независимая сторона и объективно оцениваем проводимые работы. А это значит, что Вы всегда под нашей защитой, даже в случае споров в суде.</p>
                        </div>
                    </article>

                    <article class="cc-story-item">
                        <div class="cc-story-icon"><img src="img/logo-new2.png" alt="" loading="lazy" decoding="async"></div>
                        <div class="cc-story-content">
                            <p class="cc-story-text"><strong>Индивидуальный подход и особое отношение.</strong> Каждый случай рассматриваем индивидуально, разрабатывая персональные услуги, условия и тарифы, исходя из задач и характера груза. Приоритетным условием для нас всегда является независимость и точность предоставляемой информации, и полная конфиденциальность Клиента.</p>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <?php include_once('section-service-detail.php'); ?>
        <?php include_once('section-gallery.php'); ?>
        <?php include_once('section-contact.php'); ?>
        <?php include_once('section-about.php'); ?>
    </main>
    <?php include_once('footer.php'); ?>
</div>

<?php include_once('footer-libraries.php'); ?>
</body>
</html>
