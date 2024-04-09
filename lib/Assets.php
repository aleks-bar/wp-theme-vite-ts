<?php

class Assets
{
    static private string $PATH_TO_IMAGES = IS_DEV ? THEME_URL.'/src/assets/images/' : THEME_URL.'/dist/images/';
    static private string $PATH_TO_ICONS = IS_DEV ? THEME_URL.'/src/assets/icons/' : THEME_URL.'/dist/icons/';
    static function getImage(string $image): string
    {
        return self::$PATH_TO_IMAGES . $image;
    }
    static function getIcon(string $icon): string
    {
        return self::$PATH_TO_ICONS . $icon;
    }
}