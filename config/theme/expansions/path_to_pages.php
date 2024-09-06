<?php
add_filter(
    'template_include',
    'change_pages_path'
);

function change_pages_path($template)
{
    $post = get_queried_object();
    $path = [
        'pages' => 'templates/routs/pages/',
        'single' => 'templates/routs/pages/single/',
        'archive' => 'templates/routs/pages/archive/',
        'taxonomy' => 'templates/routs/pages/taxonomy/',
    ];

    if (is_404()) {
        if (file_exists($template_404 = locate_template([$path['pages'] . '404.php']))) return $template_404;
    }

    if (is_archive()) {
        if (file_exists($post_archive_page = locate_template([$path['archive'] . $post->name . '.php']))) return $post_archive_page;
        if (file_exists($post_archive_page = locate_template([$path['archive'] . 'archive-' .  $post->name . '.php']))) return $post_archive_page;
        if (file_exists($archive_page = locate_template([$path['archive'] . 'archive.php']))) return $archive_page;
        if (file_exists($archive_page = locate_template([$path['pages'] . 'archive.php']))) return $archive_page;
    }

    if (is_single()) {
        if (file_exists($post_single_page = locate_template([$path['single'] . $post->post_type . '.php']))) return $post_single_page;
        if (file_exists($post_single_page = locate_template([$path['single'] . 'single-' . $post->post_type . '.php']))) return $post_single_page;
        if (file_exists($single_page = locate_template([$path['single'] . 'single.php']))) return $single_page;
        if (file_exists($single_page = locate_template([$path['pages'] . 'single.php']))) return $single_page;
    }

    if (!empty($post->post_type) && $post->post_type === 'page') {
        if (is_front_page()) {
            if (file_exists($front_page = locate_template([$path['pages'] . 'front-page.php']))) return $front_page;
        } else {
            $hight_priority_name = $post->post_name;
            $low_priority_name = 'page-'.$hight_priority_name;
            if (file_exists($page_template = locate_template([$path['pages'] . $hight_priority_name . '.php']))) return $page_template;
            if (file_exists($page_template = locate_template([$path['pages'] . $low_priority_name . '.php']))) return $page_template;
        }
        if (file_exists($default_page_template = locate_template([$path['pages'] . 'page.php']))) return $default_page_template;
    }

    if(is_tax()) {
        $taxonomy_name = $post->taxonomy;
        if (file_exists($tax_template = locate_template([$path['taxonomy'] . $taxonomy_name . '.php']))) return $tax_template;
        if (file_exists($tax_template = locate_template([$path['taxonomy'] . 'taxonomy-' . $taxonomy_name . '.php']))) return $tax_template;
        if (file_exists($tax_page = locate_template([$path['taxonomy'] . 'taxonomy.php']))) return $tax_page;
        if (file_exists($tax_page = locate_template([$path['pages'] . 'taxonomy.php']))) return $tax_page;
    }

    return $template;
}
