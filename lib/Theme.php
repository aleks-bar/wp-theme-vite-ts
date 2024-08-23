<?php

class Theme
{
    private static ?Theme $instance = null;

    public string $slug = 'vite-theme';

    public array $supports;
    public array $mods;

    public string $type = 'vite';

    public array $viteAssets = [];

    public static array $assets;

    private static array $enqueued_assets_data = [];
    private static string $main_chunk_name = 'index';
    private static string $vendors_main_name = 'vendors';
    private static string $variables_list_name = VARIABLES_LIST_NAME;
    private static string $vite_client_slug = 'vite-client';
    private static array $js_deps = [
        'production' => [ 'jquery', 'wp-api' ],
        'development' => [ 'jquery', 'wp-api', 'vite-client' ]
    ];
    private static bool $useAsyncChunks = true;
    private static ?string $chunkName = null;
    private static string $htmlAttributeName = 'chunk';

    private static bool $is_dev;


    public static function getInstance(): Theme
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
        self::$is_dev = IS_DEV ?? false;
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

    public function setAssetsJson($file)
    {
        if (file_exists($file)) {
            self::$assets = (array)json_decode(file_get_contents($file));
        }
    }

    private static function parseHtmlTag($html): array
    {
        $result = [
            'tag_name' => '',
            'has_closing_tag' => false,
            'content' => '',
            'attributes' => []
        ];

        // Регулярное выражение для поиска тега, его атрибутов и контента внутри
        if (preg_match('/<(\w+)([^>]*)>(.*?)<\/\1>/s', $html, $matches)) {
            $result['tag_name'] = $matches[1];
            $result['has_closing_tag'] = true;
            $result['content'] = $matches[3];
            $attributesString = $matches[2];
        } elseif (preg_match('/<(\w+)([^>]*)\/>/s', $html, $matches)) {
            $result['tag_name'] = $matches[1];
            $attributesString = $matches[2];
        } else {
            return $result; // Возвращаем пустой массив, если тег не соответствует ожидаемому формату
        }

        // Регулярное выражение для поиска атрибутов и их значений
        if (preg_match_all('/(\w+)="([^"]*)"/', $attributesString, $attributeMatches, PREG_SET_ORDER)) {
            foreach ($attributeMatches as $attribute) {
                $result['attributes'][$attribute[1]] = $attribute[2];
            }
        }

        return $result;
    }

    public function add_attributes_vite_assets($tag, $handle)
    {
        $add_module_for_tag =
            self::$useAsyncChunks && (self::isUseChunk($handle) || $handle === self::$vite_client_slug);

        if ($add_module_for_tag) {
            $parsed_tag = self::parseHtmlTag($tag);
            $parsed_tag['attributes']['type'] = 'module';
            $tag = '<'.$parsed_tag['tag_name'];

            foreach ($parsed_tag['attributes'] as $attribute => $value) {
                $tag .= ' '.$attribute.'="'.$value.'"';
            }

            $tag .= $parsed_tag['has_closing_tag'] === true
                ? '>'.$parsed_tag['content'].'</'.$parsed_tag['tag_name'].'>' : '/>';
        }

        return $tag;
    }

    private static function isUseChunk($chunk_name): bool
    {
        return str_starts_with($chunk_name, self::$main_chunk_name)
            || str_starts_with($chunk_name, self::$vendors_main_name);
    }

    private static function enqueueChunkData($chunk_name, $chunk_data): void
    {
        if (!empty($chunk_data['url_js'])) {
            wp_enqueue_script($chunk_name, $chunk_data['url_js'], self::getJsDeps(), null, true);
        }
        if (!empty($chunk_data['url_css'])) {
            wp_enqueue_style($chunk_name, $chunk_data['url_css'], null, null);
        }
    }

    private static function setChunkData($chunk_name, $link): void
    {
        $current_chunk_name = trim($chunk_name, '_');
        $field_name_for_ext = 'incorrect_field_name';
        $path_data = explode('.', $link);
        $ext  = end($path_data);

        if (empty(self::$enqueued_assets_data[$current_chunk_name])) {
            self::$enqueued_assets_data[$current_chunk_name] = [];
        }

        if ($ext === 'css') {
            $field_name_for_ext = 'url_css';
        }
        if ($ext === 'js' || $ext === 'ts' || $ext === 'tsx' || $ext === 'jsx') {
            $field_name_for_ext = 'url_js';
        }

        self::$enqueued_assets_data[$current_chunk_name]['use'] = self::isUseChunk($current_chunk_name);
        self::$enqueued_assets_data[$current_chunk_name][$field_name_for_ext] = $link;
    }

    private static function getJsDeps(): array
    {
        return self::$is_dev ? self::$js_deps['development'] : self::$js_deps['production'];
    }


    private static function enqueueChunks(): void
    {
//        $chunks_count = count(self::$enqueued_assets_data);
//        if ($chunks_count === 1) {
//            $chunk_name = array_key_first(self::$enqueued_assets_data);
//            self::$enqueued_assets_data[$chunk_name]['use'] = true;
//        }

        foreach (self::$enqueued_assets_data as $chunk_name => $chunk_data) {
            if ($chunk_data['use']) {
                self::enqueueChunkData($chunk_name, $chunk_data);
            }
        }
    }

    public function injectViteAssets()
    {
        if (!empty(self::$assets)) {
            if (self::$is_dev) {
                if (empty(self::$assets['inputs'])) {
                    wp_die('Неверно указан параметр WP_ENV');
                }

                $url = self::$assets['url'];
                wp_enqueue_script(self::$vite_client_slug, $url . '@vite/client', self::$js_deps['production'], null, true);

                foreach ((array)self::$assets['inputs'] as $chunk_name => $link) {
                    self::setChunkData($chunk_name, $url.$link);
                }
            } else {
                if (!empty(self::$assets['inputs'])) {
                    wp_die('Неверно указан параметр WP_ENV');
                }

                foreach (self::$assets as $local_asset_path => $data) {
                    $url = get_theme_file_uri(THEME_BUILD_DIR . '/' . $data->file);
                    $path_to_file_array = explode('/', $local_asset_path);
                    $chunk_name = explode('.', end($path_to_file_array))[0];

                    self::setChunkData($chunk_name, $url);
                }
            }

            self::enqueueChunks();

            add_filter('script_loader_tag', [$this, 'add_attributes_vite_assets'], 10, 999);
        }
    }

    public function addVariablesForJs($variables = [])
    {
        if (!empty($variables)) {
            wp_localize_script(self::$main_chunk_name, self::$variables_list_name, $variables);
        }
    }

    public function setLoadAsyncChunks($enable): void
    {
        self::$useAsyncChunks = $enable;
    }

    static function setChunkName(string $chunkName): void
    {
        self::$chunkName = $chunkName;
    }

    static function addChunkIfEnabled(): ?string
    {
        if (!empty(self::$chunkName) && !empty(self::$useAsyncChunks)) {
            return self::$htmlAttributeName.'="'.self::$chunkName.'"';
        }
        return null;
    }
}
