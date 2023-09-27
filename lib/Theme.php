<?php

class Theme
{
    private static ?Theme $instance = null;

    public string $slug = 'vite-theme';

    public array $supports;
    public array $mods;

    public string $type = 'vite';

    public array $viteAssets = [];
    public array $webpackAssets = [];

    public static array $assets;

    public static function getInstance(): Theme
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
        // Получаем данные из файла assets.json
        if (file_exists(get_theme_file_path('/dist/manifest.json'))) {
            self::$assets = (array) json_decode(file_get_contents(get_theme_file_path('/dist/manifest.json')));
        }
    }

    private function __clone()
    {
    }

    /**
     * @throws Exception
     */
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }

    public function addThemeSupport(string $name, $args = []): void
    {
        $this->supports[$name] = $args;
        add_theme_support($name, $args);
    }

    public function addSvgUploads(): void
    {
        /**
         * Включаем поддержку svg
         */
        add_filter('upload_mimes', function ($mimes) {
            $mimes['svg'] = 'image/svg+xml';

            return $mimes;
        });

        /**
         * Исправление MIME типа для SVG файлов.
         */
        add_filter('wp_check_filetype_and_ext', function ($data, $file, $filename, $mimes, $real_mime = '') {
            if (version_compare($GLOBALS['wp_version'], '5.1.0', '>=')) {
                $dosvg = in_array($real_mime, [
                    'image/svg',
                    'image/svg+xml'
                ]);
            } else {
                $dosvg = ( '.svg' === strtolower(substr($filename, -4)) );
            }

            if ($dosvg) {
                if (current_user_can('manage_options')) {
                    $data['ext']  = 'svg';
                    $data['type'] = 'image/svg+xml';
                } else {
                    $data['ext'] = $type_and_ext['type'] = false;
                }
            }
            return $data;
        }, 10, 5);

        /**
         * Формирует данные для отображения SVG как изображения в медиабиблиотеке.
         *
         * @param $response
         *
         * @return mixed
         */
        function show_svg_in_media_library($response)
        {
            if ($response['mime'] === 'image/svg+xml') {
                // С выводом названия файла
                $response['image'] = [
                    'src' => $response['url'],
                ];
            }
            return $response;
        }

        add_filter('wp_prepare_attachment_for_js', 'show_svg_in_media_library');
    }

    /**
     * Установим максимальное количество ревизий записи
     */
    public function setCountRevisions($number): void
    {
        if (!defined('WP_POST_REVISIONS')) {
            define('WP_POST_REVISIONS', $number);
        }
    }

    /**
     * Отключаем Emoji
     * @return void
     */
    public function disableEmojis(): void
    {
        add_action('init', function () {
            remove_action('wp_head', 'print_emoji_detection_script', 7);
            remove_action('admin_print_scripts', 'print_emoji_detection_script');
            remove_action('wp_print_styles', 'print_emoji_styles');
            remove_action('admin_print_styles', 'print_emoji_styles');
            remove_filter('the_content_feed', 'wp_staticize_emoji');
            remove_filter('comment_text_rss', 'wp_staticize_emoji');
            remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
        });
    }

    /**
     * Добавляем миниатюру к кастомным типам постов
     * @param array $ptype_names
     * @return void
     */
    public function addThumbnailPostsInAdmin($ptype_names = []): void
    {
        add_action('init', 'add_post_thumbs_in_post_list_table', 20);
        function add_post_thumbs_in_post_list_table(): void
        {
            // проверим какие записи поддерживают миниатюры
            $supports = get_theme_support('post-thumbnails');

            // Определяем типы записей автоматически
            if (!isset($ptype_names)) {
                if ($supports === true) {
                    $ptype_names = get_post_types([ 'public' => true ], 'names');
                    $ptype_names = array_diff($ptype_names, [ 'attachment' ]);
                } // для отдельных типов записей
                elseif (is_array($supports)) {
                    $ptype_names = $supports[0];
                }
            }

            // добавляем фильтры для всех найденных типов записей
            foreach ($ptype_names as $ptype) {
                add_filter("manage_{$ptype}_posts_columns", 'add_thumb_column');
                add_action("manage_{$ptype}_posts_custom_column", 'add_thumb_value', 10, 2);
            }
        }

        // добавим колонку
        function add_thumb_column($columns)
        {
            // подправим ширину колонки через css
            add_action('admin_notices', function () {
                echo '
			<style>
				.column-thumbnail{ width:80px; text-align:center; }
			</style>';
            });

            $num = 1; // после какой по счету колонки вставлять новые

            $new_columns = [ 'thumbnail' => __('Thumbnail') ];

            return array_slice($columns, 0, $num) + $new_columns + array_slice($columns, $num);
        }

        // заполним колонку
        function add_thumb_value($colname, $post_id)
        {
            if ('thumbnail' == $colname) {
                $width = $height = 45;

                // миниатюра
                if ($thumbnail_id = get_post_meta($post_id, '_thumbnail_id', true)) {
                    $thumb = wp_get_attachment_image($thumbnail_id, [
                        $width,
                        $height
                    ], true);
                } // из галереи...
                elseif ($attachments = get_children(
                    [
                        'post_parent'    => $post_id,
                        'post_mime_type' => 'image',
                        'post_type'      => 'attachment',
                        'numberposts'    => 1,
                        'order'          => 'DESC',
                    ]
                )
                ) {
                    $attach = array_shift($attachments);
                    $thumb  = wp_get_attachment_image($attach->ID, [
                        $width,
                        $height
                    ], true);
                }

                echo empty($thumb) ? ' ' : $thumb;
            }
        }
    }

    /**
     * Отключение полноэкранного режима в gutenberg
     * @return void
     */
    public function disableEditorFullScreen(): void
    {
        if (is_admin()) {
            function jba_disable_editor_fullscreen_by_default()
            {
                $script = "jQuery( window ).load(function() { const isFullscreenMode = wp.data.select( 'core/edit-post' ).isFeatureActive( 'fullscreenMode' ); if (isFullscreenMode) { wp.data.dispatch( 'core/edit-post' ).toggleFeature( 'fullscreenMode' ); } });";
                wp_add_inline_script('wp-blocks', $script);
            }
            add_action('enqueue_block_editor_assets', 'jba_disable_editor_fullscreen_by_default');
        }
    }

    /**
     * Скрываем пункты из меню
     */
    public function disableItemsAdminMenu(array $items = []): void
    {
        add_action("admin_menu", function () use ($items) {
            foreach ($items as $item) {
                remove_menu_page($item);
            }
        });
    }

    public function setAssetsJson($file, $type = 'vite')
    {
        $this->type = $type;

        if ($this->type === 'vite') {
            // Получаем данные из файла assets.json
            if (file_exists($file)) {
                $this->viteAssets = (array)json_decode(file_get_contents($file));
            }
        }

        if ($this->type === 'webpack') {
            // Получаем данные из файла assets.json
            if (file_exists($file)) {
                $this->webpackAssets = (array)json_decode(file_get_contents($file));
            }
        }


    }

    public function add_attributes_vite_assets($tag, $handle, $src)
    {
        $isDev = THEME_MOD === 'development';

        if ($isDev) {
            if ((array_key_exists($handle, (array)self::$assets['inputs'] )) || $handle === 'vite-client') {
                $tag = '<script type="module" src="' . esc_url($src) . '"></script>';
            }
        }

        return $tag;
    }

    public function injectViteAssets()
    {
        if (self::$assets) {
            $is_dev = THEME_MOD === 'development';

            if ($is_dev) {
                $url = self::$assets['url'];
                wp_enqueue_script('vite-client', $url . '@vite/client', [ 'jquery', 'wp-api' ], null, true);

                foreach ((array)self::$assets['inputs'] as $name => $link) {
                    wp_enqueue_script($name, $url . $link, [ 'jquery', 'wp-api', 'vite-client' ], null, true);
                }
            } else {
                foreach (self::$assets as $name => $chunk) {
                    $data = explode('.', $name);
                    $ext  = end($data);

                    // Получение полного пути до чанка
                    $url = get_theme_file_uri(THEME_BUILD_DIR . '/' . $chunk->file);


                    if ($ext === 'css') {
                        wp_enqueue_style($name, $url, null, null);
                    } elseif ($ext === 'js' || $ext === 'ts' || $ext === 'tsx' || $ext === 'jsx') {
                        wp_enqueue_script($name, $url, [ 'jquery', 'wp-api' ], null, true);
                    }
                }
            }

            add_filter('script_loader_tag', [$this, 'add_attributes_vite_assets'], 10, 999);
        }
    }

    public function addVariablesForJs($script_name, $variables = [], $main_variable_name = 'site')
    {
        if (!empty($variables) && !empty($script_name)) {
            wp_localize_script($script_name, $main_variable_name, $variables);
        }
    }

    public function getUrlChunk($name): ?string
    {
        $is_dev = THEME_MOD === 'development';

        if ($this->type === 'vite') {
            $assets = (array)$this->viteAssets;

            if (!empty($assets[$name]) && !$is_dev) {
                return get_theme_file_uri(THEME_BUILD_DIR . '/' . $assets[$name]->file);
            } else {
                return get_theme_file_uri($name);
            }
        }

        return null;
    }

    public function localizeScript($name) {
        $is_dev = THEME_MOD === 'development';

        if ($this->type === 'vite') {

            wp_localize_script( $name, 'myajax',
                array(
                    'url' => admin_url('admin-ajax.php')
                )
            );
        }
    }
}
