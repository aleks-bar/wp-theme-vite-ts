<?php

$theme = Theme::getInstance();

/**
 * Включение миниатюр записи
 */
add_theme_support('post-thumbnails');

/**
 * Включение поддержку меню
 */
add_theme_support('menus');

/**
 * Поддержка тега title
 */
add_theme_support('title-tag');

/**
 * Поддержка кастомных стилей для гутенберга
 */
add_theme_support('editor-styles');

/**
 * Включает поддержку широкого выравнивания для картинок у блоков Гутенберга
 */
add_theme_support('align-wide');

/**
 * Add custom background
 */
add_theme_support('custom-background', [
    'default-color'          => '#fff',
    'default-image'          => '',
    'wp-head-callback'       => '_custom_background_cb',
    'admin-head-callback'    => '',
    'admin-preview-callback' => ''
]);

/**
 * Включает поддержку html5 разметки для списка комментариев,
 * формы комментариев, формы поиска, галереи и т.д. Где нужно включить разметку указывается во втором параметре:
 */
add_theme_support('html5', [
    'comment-list',
    'comment-form',
    'search-form',
    'gallery',
    'caption',
    'script',
    'style',
]);

/**
 * Add support logo
 */
add_theme_support('custom-logo', [
    'height'      => 190,
    'width'       => 190,
    'flex-width'  => true,
    'flex-height' => true,
    'header-text' => '',
    'unlink-homepage-logo' => true, // WP 5.5
]);

/**
 * Добавляем поддержку SVG
 */
$theme->addSvgUploads();

/**
 * Установим максимальное количество ревизий записи
 */
$theme->setCountRevisions(5);

/**
 * Отключаем Emoji
 */
$theme->disableEmojis();

/**
 * Отключение полноэкранного режима в gutenberg
 */
$theme->disableEditorFullScreen();


$theme->disableItemsAdminMenu([
    // 'index.php', // Отключаем пункт меню в консоле
    // 'edit.php', // Отключаем пункт меню записи
    // 'edit-comments.php', // Комментарии
    // 'users.php', // Пользователи
    // 'options-general.php', // параметры
]);

/**
 * Удаляет "Рубрика: ", "Метка: " и т.д. из заголовка архива
 */
add_filter( 'get_the_archive_title', function( $title ){
    return preg_replace('~^[^:]+: ~', '', $title );
});
