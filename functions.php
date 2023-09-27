<?php
use Idleberg\WordpressViteAssets\WordpressViteAssets;

/*
|--------------------------------------------------------------------------
| Регистрация авто загрузчика composer
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| our theme. We will simply require it into the script here so that we
| don't have to worry about manually loading any of our classes later on.
|
*/
if (! file_exists($composer = __DIR__ . '/vendor/autoload.php')) {
    wp_die(__('Ошибка загрузки. Пожалуйста выполните <code>composer install</code> в папке активной темы', 'sage'));
}

require_once $composer;
