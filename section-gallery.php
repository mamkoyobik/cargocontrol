<?php
$galleryGroups = [
    [
        'slug' => 'rokonord',
        'title' => 'Перегрузка терминал "РокоНорд"',
        'folder' => 'gallery1',
        'alt' => 'Фотогалерея Cargocontrol'
    ],
    [
        'slug' => 'yuzhny',
        'title' => 'Таможенный досмотр пост "Южный"',
        'folder' => 'gallery2',
        'alt' => 'Фотогалерея Cargocontrol'
    ],
    [
        'slug' => 'korund-terminal',
        'title' => 'Перегрузка терминал "Корунд"',
        'folder' => 'gallery3',
        'alt' => 'Фотогалерея Cargocontrol'
    ],
    [
        'slug' => 'nika',
        'title' => 'Перегрузка склад "Ника"',
        'folder' => 'gallery4',
        'alt' => 'Фотогалерея Cargocontrol'
    ],
    [
        'slug' => 'korund-customs',
        'title' => 'Таможенный досмотр терминал "Корунд"',
        'folder' => 'gallery5',
        'alt' => 'Фотогалерея Cargocontrol'
    ],
    [
        'slug' => 'damage-registration',
        'title' => 'Регистрация повреждений',
        'folder' => 'gallery6',
        'alt' => 'Фотогалерея Cargocontrol'
    ],
    [
        'slug' => 'rail-container',
        'title' => 'Перегрузка в ж/д контейнер',
        'folder' => 'gallery7',
        'alt' => 'Фотогалерея Cargocontrol'
    ],
];
?>
<section id="gallery" class="cc-section cc-gallery-section">
    <div class="container">
        <header class="section_header text-center">
            <h2><strong>Фото</strong>галерея</h2>
        </header>

        <div class="cc-gallery-tabs" role="tablist" aria-label="Фотогалерея">
            <?php foreach ($galleryGroups as $index => $group) { ?>
                <button
                    type="button"
                    class="cc-gallery-tab<?php echo $index === 0 ? ' is-active' : ''; ?>"
                    id="gallery-tab-<?php echo $group['slug']; ?>"
                    data-gallery-target="gallery-panel-<?php echo $group['slug']; ?>"
                    role="tab"
                    aria-controls="gallery-panel-<?php echo $group['slug']; ?>"
                    aria-selected="<?php echo $index === 0 ? 'true' : 'false'; ?>"
                >
                    <?php echo $group['title']; ?>
                </button>
            <?php } ?>
        </div>

        <div class="cc-gallery-panels">
            <?php foreach ($galleryGroups as $index => $group) { ?>
                <?php $isActive = $index === 0; ?>
                <section
                    id="gallery-panel-<?php echo $group['slug']; ?>"
                    class="cc-gallery-panel<?php echo $isActive ? ' is-active' : ''; ?>"
                    role="tabpanel"
                    aria-labelledby="gallery-tab-<?php echo $group['slug']; ?>"
                    aria-hidden="<?php echo $isActive ? 'false' : 'true'; ?>"
                    <?php echo $isActive ? '' : 'hidden'; ?>
                >
                    <h3><?php echo $group['title']; ?></h3>
                    <div class="cc-gallery-grid">
                        <?php for ($i = 1; $i < 7; $i++) { ?>
                            <?php $isExtraImage = $i > 4; ?>
                            <a
                                class="cc-gallery-item<?php echo $isExtraImage ? ' is-extra' : ''; ?>"
                                href="<?php echo $group['folder']; ?>/<?php echo $i; ?>.jpg"
                                data-lightbox-group="<?php echo $group['slug']; ?>"
                                data-lightbox-index="<?php echo $i - 1; ?>"
                                <?php echo $isExtraImage ? 'hidden' : ''; ?>
                            >
                                <img
                                    loading="lazy"
                                    decoding="async"
                                    src="<?php echo $group['folder']; ?>/<?php echo $i; ?>.jpg"
                                    alt="<?php echo $group['title']; ?>, фото <?php echo $i; ?>"
                                >
                            </a>
                        <?php } ?>
                    </div>
                    <button
                        type="button"
                        class="cc-gallery-toggle"
                        data-gallery-toggle
                        aria-expanded="false"
                    >
                        Показать все фото
                    </button>
                </section>
            <?php } ?>
        </div>
    </div>
</section>

<div id="ccLightbox" class="cc-lightbox" aria-hidden="true">
    <button type="button" class="cc-lightbox-close" aria-label="Закрыть">&times;</button>
    <button type="button" class="cc-lightbox-nav cc-lightbox-prev" aria-label="Предыдущее изображение"></button>
    <img class="cc-lightbox-image" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==" alt="">
    <button type="button" class="cc-lightbox-nav cc-lightbox-next" aria-label="Следующее изображение"></button>
</div>
