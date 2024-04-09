<?php

class Template extends TemplateError
{
    private static array $paths = [
        'root' => '/templates/',
        'shared' => '/templates/shared/',
        'sections' => '/templates/sections/',
    ];

    static function shared(string $template, array $args = []): void
    {
        self::get($template, $args, 'shared');
    }

    static function section(string $template, array $args = []): void
    {
        self::get($template, $args, 'sections');
    }

    static function getCurrentSection($section, $sections_global, $index = null, $get_html = false)
    {
        $current_section = null;
        $section_name = $section['acf_fc_layout'];

        if (self::isGlobalSection($section)) {
            if (!empty($sections_global) && in_array($section_name, array_column($sections_global, 'acf_fc_layout'))) {
                $current_section = self::getSectionByName($section_name, $sections_global);
            } else {
                self::error('getPageSections: в глобальных секциях отсутствует "' . $section_name . '"');
            }
        } else {
            $current_section = $section;
        }

        if (!empty($current_section)) {
            unset($current_section['acf_fc_layout']);
            unset($current_section['sec_type']);

            $section_args = $index !== null ? array_merge($current_section, ['sections_index' => $index]) : $current_section;

            if ($get_html === true) {
                ob_start();
            }

            self::section($section_name, $section_args);

            if ($get_html === true) {
                return ob_get_clean();
            }
            return true;
        }
        return false;
    }

    static function getSectionsData($sections, $params): array
    {
        $sections_html = [];
        $sections_global = get_field('sections', 'global_sections');
        $current_index = !empty($params['index']) ? (int) $params['index'] : 0;
        $group_length = !empty($params['group']) ? (int) $params['group'] : 1;
        $last_index = 0;
        $sections_length = count($sections);

        foreach ($sections as $index => $section) {
            if ($index >= $current_index && $index <= $current_index + $group_length - 1) {
                $sections_html[] = self::getCurrentSection($section, $sections_global, $index, true);
                $last_index = $index;
            }
        }

        return [
            'sections' => $sections_html,
            'last_index' => $last_index,
            'sections_length' => $sections_length
        ];
    }

    static function getPageSections($sections = [], $post_id = null, $showed_sections_count = 3): void
    {
        $sections_global = get_field('sections', 'global_sections');
        $new_sections = array_slice($sections, 0, $showed_sections_count);
        $last_index = !empty($new_sections) ? array_key_last($new_sections) : 0;
        ?>
          <div id="sections-wrapper">
              <div id="sections" <?= !empty($post_id) ? 'sections-page="'.$post_id.'"' : null ?> sections-last-index="<?= $last_index ?>">
                  <?php foreach ($new_sections as $index => $section) {
                      if (!empty($section['no_load']) && $section['no_load'] === true) {
                          continue;
                      }
                        self::getCurrentSection($section, $sections_global, $index);
                  } ?>
              </div>
              <div id="sections-loader" class="py-10 flex justify-center w-full hidden">
                  <svg class="w-16 h-16 animate-spinner fill-accent">
                      <use href="#loader">
                  </svg>
              </div>
          </div>
        <?php
    }

    static function getSectionByName(string $section_name, $sections = [])
    {
        if (!empty($sections)) {
            foreach ($sections as $section) {
                if ($section['acf_fc_layout'] === $section_name) {
                    return $section;
                }
            }
        }
    }

    static function error(string $error_text): void
    {
        self::showError($error_text, 'Template-Error', 'проверьте передаваемые параметры');
    }
    private static function isGlobalSection($section)
    {
        if (!empty($section['sec_type'])) {
            return $section['sec_type'] === 'global';
        } else {
            self::error('section-template: отсутствует поле "sec_type"');
        }
    }
    private static function get(string $template, array $args = [], string $type = 'root'): void
    {
        $path_to_file = self::getTemplatePathInTheme($template, $type);

        if (file_exists(THEME_PATH.$path_to_file)) {
            get_template_part(self::$paths[$type].$template.'/template', null, $args);
        } else {
            self::error('missing-file: в теме отсутствует файл "'.$path_to_file.'"');
        }
    }
    static function getFile(string $template, array $args = [], string $type = 'root'): void
    {
        $path_to_file = self::getFilePathInTheme($template, $type);

        if (file_exists(THEME_PATH.$path_to_file)) {
            get_template_part(self::$paths[$type].$template, null, $args);
        } else {
            self::error('missing-file: в теме отсутствует файл "'.$path_to_file.'"');
        }
    }

    private static function getTemplatePathInTheme(string $template, string $type = 'root'): string
    {
        return self::$paths[$type].$template.'/template.php';
    }
    private static function getFilePathInTheme(string $template, string $type = 'root'): string
    {
        return self::$paths[$type].$template.'.php';
    }
}
