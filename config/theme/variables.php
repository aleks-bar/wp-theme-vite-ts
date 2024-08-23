<?php
use function Env\env;

/*
|--------------------------------------------------------------------------
| Получение данных из .env файла
|--------------------------------------------------------------------------
*/
$env_files = file_exists(get_theme_file_path('.env.local'))
    ? [ '.env', '.env.local' ]
    : [ '.env' ];

$dotenv = Dotenv\Dotenv::createUnsafeImmutable(get_template_directory(), $env_files, false);

if (file_exists(get_theme_file_path('.env'))) {
    $dotenv->load();
}

/*
|--------------------------------------------------------------------------
| Объявление переменных темы
|--------------------------------------------------------------------------
*/

define('THEME_HOST', env('HOST') ?? 'localhost');
define('THEME_PORT', env('PORT') ?? '3000');
define('THEME_BUILD_DIR', env('BUILD_DIR') ?? 'dist');
define('THEME_PUBLIC_DIR', env('PUBLIC_DIR') ?? 'public');
define('THEME_MANIFEST', env('MANIFEST') ?? 'manifest.json');
define('THEME_URL', get_template_directory_uri());
define('THEME_PATH', get_theme_file_path());

define('VARIABLES_LIST_NAME', env('VARIABLES_LIST_NAME') ?? 'variables');

define('PAGE_GIRLS_ID', 114);

define('YMAPS_API_KEY', '3f5a2cdc-68f4-42af-9ab5-143cb3d760ee');