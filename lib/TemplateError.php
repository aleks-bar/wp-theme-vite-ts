<?php

class TemplateError
{
    protected static function showError(string $error_text, string $error_type, string $default_error): void
    {
        if (IS_DEV) {
            $error_html_data = '<div class="flex flex-col bg-dark border-red border py-1 px-2 my-1 text-white w-fit mx-5">'.
                '<span class="text-red">'.$error_type.'</span>';
            $error_html_data .= !empty($error_text) ?
                '<span>'.$error_text.'</span>' : '<span>'.$default_error.'</span>';

            $error_html_data .= '</div>';

            print_r($error_html_data);
        }
    }
}