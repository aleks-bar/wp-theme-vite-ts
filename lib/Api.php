<?php

namespace Theme;

/*
 * Лучше используй этот класс для обертки API он будет создавать эндпоинты
 */
class Api
{
    public string $namespace;
    public string $version;
    public static array $endPoints = [];

    // Инициализируем новое апи
    public function __construct($params) {
        $this->namespace = $params['namespace'] ?? 'theme';
        $this->version = $params['version'] ?? 'v1';
    }

    private function create($method, $params)
    {
        $default = [
            'methods'   => $method,
            'namespace' => "$this->namespace/$this->version",
        ];

        self::$endPoints[] = array_merge($default, $params);
    }

    public function Get($url, $args = [], $callback = null, $permission_callback = '__return_true')
    {
        $this->create('GET', [
            'url'                 => $url,
            'callback'            => $callback,
            'args'                => $args,
            'permission_callback' => $permission_callback,
        ]);
    }

    public function Post($url, $args = [], $callback = null, $permission_callback = '__return_true')
    {
        $this->create('POST', [
            'url'                 => $url,
            'callback'            => $callback,
            'args'                => $args,
            'permission_callback' => $permission_callback,
        ]);
    }

    public function init()
    {
        add_action('rest_api_init', function () {
            if (self::$endPoints) {
                foreach (self::$endPoints as $point) {
                    register_rest_route($point['namespace'], $point['url'], [
                        'methods'             => $point['methods'],
                        'callback'            => $point['callback'],
                        'args'                => $point['args'],
                        'permission_callback' => $point['permission_callback'] ?? '__return_true',
                    ]);
                }
            }
        });
    }
}
