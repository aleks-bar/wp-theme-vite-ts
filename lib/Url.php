<?php

class Url
{
    /**
     * @param bool $with_params
     *
     * @return string
     */
    public static function get(bool $with_params = false): string
    {
        $url = ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $url = explode('?', $url);
        return $with_params ? $url : $url[0];
    }
    public static function getTermLink(string $taxonomy, int $term_id, $url_params = [], $prefix = ''): string
    {
        $term_link_params = [];
        if (!empty($taxonomy) && !empty($term_id)) {
            $term_link_params = ['taxonomy' => $taxonomy, 'terms' => [$term_id]];
        }
        $main_url_data = self::arrayToQueryString(array_merge($url_params, $term_link_params));

        return !empty($prefix) ? get_home_url() . $prefix . $main_url_data : self::get() . $main_url_data;
    }

    /**
     * @return array
     */
    static function getChangedWpParams(): array
    {
        return [
            'page' => 'paged',
        ];
    }

    /**
     * @param array $params
     *
     * @return array
     */
    static function convertParamsToWpQuery(array $params = []): array
    {
        $query = [];

        if (!empty($params)) {
            $changed_params = self::getChangedWpParams();
            $tax_query_template = [
                'taxonomy' => null,
                'field' => 'id',
                'terms' => null,
            ];


            foreach ($params as $paramKey => $paramValue) {
                if ($paramKey === 'terms' || $paramKey === 'taxonomy') {
                    $tax_query_template['taxonomy'] = $paramKey === 'taxonomy' ? $paramValue : $tax_query_template['taxonomy'];
                    $tax_query_template['terms'] = $paramKey === 'terms' ? $paramValue : $tax_query_template['terms'];
                } elseif (in_array($paramKey, array_keys($changed_params))) {
                    $query[$changed_params[$paramKey]] = $paramValue;
                } else {
                    $query[$paramKey] = $paramValue;
                }
            }

            if (!empty($tax_query_template['taxonomy']) && !empty($tax_query_template['terms'])) {
                $query['tax_query'] = [];
                $query['tax_query'][] = $tax_query_template;
            }
        }

        return $query;
    }

    /**
     * @return array
     */
    static function getParams(string $url = ''): array
    {

        if (!empty($url)) {
            [$path, $params] = explode('?', $url);
            parse_str($params, $queryParams);
        } else {
            parse_str($_SERVER['QUERY_STRING'], $queryParams);
        }

        return $queryParams;
    }

    public static function arrayToQueryString($params): string
    {
        $new_params = [];
        $changed_params = self::getChangedWpParams();

        if (!empty($params)) {
            foreach ($params as $paramKey => $paramValue) {
                if ($paramKey === 'tax_query') {
                    $new_params['taxonomy'] = $params['tax_query'][0]['taxonomy'];
                    $new_params['terms'] = implode(",", $params['tax_query'][0]['terms']);
                } elseif (in_array($paramKey, array_values($changed_params))) {
                    $new_param_key = array_search($paramKey, $changed_params);
                    $new_params[$new_param_key] = $paramValue;
                } else {
                    $new_params[$paramKey] = $paramValue;
                }
            }
        }

        return !empty($new_params) ? '?'.http_build_query($new_params) : '';
    }
}
