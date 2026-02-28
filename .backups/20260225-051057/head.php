<?php
$siteUrl = 'https://cargocontrol.ru/';
$siteName = 'CARGO CONTROL';

$seoTitle = isset($seoTitle) ? trim($seoTitle) : 'Грузовой сюрвейер в Санкт-Петербурге - CARGO CONTROL';
$seoDescription = isset($seoDescription)
    ? trim($seoDescription)
    : 'Независимый грузовой сюрвейер CARGO CONTROL: инспекции грузов в портах, на терминалах и складах СПб и ЛО, фотоотчет, контроль погрузки и защита от убытков. Работаем 24 часа ежедневно.';
$canonicalUrl = isset($canonicalUrl) ? trim($canonicalUrl) : $siteUrl;
$seoImage = isset($seoImage) ? trim($seoImage) : ($siteUrl . 'img/background.jpg');
$seoImageWidth = isset($seoImageWidth) ? (int)$seoImageWidth : 2560;
$seoImageHeight = isset($seoImageHeight) ? (int)$seoImageHeight : 630;

$googleVerification = getenv('GOOGLE_SITE_VERIFICATION');
$yandexVerification = getenv('YANDEX_VERIFICATION');

$schema = [
    '@context' => 'https://schema.org',
    '@graph' => [
        [
            '@type' => 'WebSite',
            '@id' => $siteUrl . '#website',
            'url' => $siteUrl,
            'name' => $siteName,
            'inLanguage' => 'ru-RU',
        ],
        [
            '@type' => 'Organization',
            '@id' => $siteUrl . '#organization',
            'name' => $siteName,
            'url' => $siteUrl,
            'logo' => [
                '@type' => 'ImageObject',
                'url' => $siteUrl . 'img/logo-new.png',
                'width' => 481,
                'height' => 65,
            ],
            'email' => 'cargo@cargocontrol.ru',
            'telephone' => '+7 (812) 921-30-74',
            'address' => [
                '@type' => 'PostalAddress',
                'postalCode' => '198096',
                'addressCountry' => 'RU',
                'addressLocality' => 'Санкт-Петербург',
                'streetAddress' => '3-й район морского порта, д.4, стр.1, пом. 5-Н',
            ],
        ],
        [
            '@type' => 'ProfessionalService',
            '@id' => $siteUrl . '#service',
            'name' => $siteName,
            'url' => $siteUrl,
            'image' => [
                $siteUrl . 'img/background.jpg',
                $siteUrl . 'img/logo-new.png',
            ],
            'description' => $seoDescription,
            'telephone' => '+7 (812) 921-30-74',
            'email' => 'cargo@cargocontrol.ru',
            'address' => [
                '@type' => 'PostalAddress',
                'postalCode' => '198096',
                'addressCountry' => 'RU',
                'addressLocality' => 'Санкт-Петербург',
                'streetAddress' => '3-й район морского порта, д.4, стр.1, пом. 5-Н',
            ],
            'areaServed' => [
                '@type' => 'Country',
                'name' => 'RU',
            ],
            'serviceType' => [
                'Сюрвейерские услуги',
                'Инспекция грузов',
                'Контроль погрузки и выгрузки',
                'Фото- и видеофиксация состояния груза',
            ],
            'openingHoursSpecification' => [
                [
                    '@type' => 'OpeningHoursSpecification',
                    'dayOfWeek' => [
                        'Monday',
                        'Tuesday',
                        'Wednesday',
                        'Thursday',
                        'Friday',
                        'Saturday',
                        'Sunday',
                    ],
                    'opens' => '00:00',
                    'closes' => '23:59',
                ],
            ],
        ],
        [
            '@type' => 'WebPage',
            '@id' => $siteUrl . '#webpage',
            'url' => $siteUrl,
            'name' => $seoTitle,
            'description' => $seoDescription,
            'inLanguage' => 'ru-RU',
            'isPartOf' => [
                '@id' => $siteUrl . '#website',
            ],
            'about' => [
                '@id' => $siteUrl . '#organization',
            ],
        ],
    ],
];
?>
<meta charset="utf-8">
<!--[if IE]>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<![endif]-->
<title><?php echo htmlspecialchars($seoTitle, ENT_QUOTES, 'UTF-8'); ?></title>
<meta name="description" content="<?php echo htmlspecialchars($seoDescription, ENT_QUOTES, 'UTF-8'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="theme-color" content="#103a61">
<meta name="robots" content="index,follow,max-snippet:-1,max-image-preview:large,max-video-preview:-1">
<meta name="googlebot" content="index,follow,max-snippet:-1,max-image-preview:large,max-video-preview:-1">
<meta name="referrer" content="strict-origin-when-cross-origin">
<meta name="format-detection" content="telephone=no">

<?php if (!empty($googleVerification)) { ?>
    <meta name="google-site-verification" content="<?php echo htmlspecialchars($googleVerification, ENT_QUOTES, 'UTF-8'); ?>">
<?php } ?>
<?php if (!empty($yandexVerification)) { ?>
    <meta name="yandex-verification" content="<?php echo htmlspecialchars($yandexVerification, ENT_QUOTES, 'UTF-8'); ?>">
<?php } ?>

<link rel="canonical" href="<?php echo htmlspecialchars($canonicalUrl, ENT_QUOTES, 'UTF-8'); ?>">
<link rel="alternate" hreflang="ru-RU" href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>">
<link rel="alternate" hreflang="x-default" href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>">

<meta property="og:locale" content="ru_RU">
<meta property="og:type" content="website">
<meta property="og:site_name" content="<?php echo htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8'); ?>">
<meta property="og:title" content="<?php echo htmlspecialchars($seoTitle, ENT_QUOTES, 'UTF-8'); ?>">
<meta property="og:description" content="<?php echo htmlspecialchars($seoDescription, ENT_QUOTES, 'UTF-8'); ?>">
<meta property="og:url" content="<?php echo htmlspecialchars($canonicalUrl, ENT_QUOTES, 'UTF-8'); ?>">
<meta property="og:image" content="<?php echo htmlspecialchars($seoImage, ENT_QUOTES, 'UTF-8'); ?>">
<meta property="og:image:width" content="<?php echo $seoImageWidth; ?>">
<meta property="og:image:height" content="<?php echo $seoImageHeight; ?>">
<meta property="og:image:alt" content="Независимый грузовой сюрвейер CARGO CONTROL">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?php echo htmlspecialchars($seoTitle, ENT_QUOTES, 'UTF-8'); ?>">
<meta name="twitter:description" content="<?php echo htmlspecialchars($seoDescription, ENT_QUOTES, 'UTF-8'); ?>">
<meta name="twitter:image" content="<?php echo htmlspecialchars($seoImage, ENT_QUOTES, 'UTF-8'); ?>">

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="apple-touch-icon" href="/apple-touch-icon-precomposed.png">
<link rel="manifest" href="/site.webmanifest">
<link rel="sitemap" type="application/xml" title="Sitemap" href="/sitemap.xml">

<link rel="preload" as="image" href="/img/background.jpg" fetchpriority="high">
<link rel="preload" as="image" href="/img/logo-new.png">

<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="css/fonts.css">
<link rel="stylesheet" href="style.css">

<link rel="preconnect" href="https://www.google.com" crossorigin>
<link rel="preconnect" href="https://www.gstatic.com" crossorigin>

<script type="application/ld+json"><?php echo json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?></script>
<script src="js/vendor/modernizr-2.6.2.min.js"></script>

<!--[if lt IE 9]>
<script src="js/vendor/html5shiv.min.js"></script>
<script src="js/vendor/respond.min.js"></script>
<![endif]-->

<script src="https://www.google.com/recaptcha/api.js" async defer></script>
