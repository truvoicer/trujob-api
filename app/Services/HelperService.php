<?php

namespace App\Services;

class HelperService
{
    public static function toSnakeCase(string $text) {
        return str_replace('-', '_', $text);
    }
    public static function toSlug(string $text) {
        $replace = str_replace(
            [
                ' ',
                '_'
            ],
            [
                '-'
            ],
            $text
        );
        return str_replace(['.',',','\'', "", '!', '$', '£'], '', strtolower($replace));
    }

    public static function snakeCaseCamelCase(string $input, ?string $separator = "_")
    {
        $splitFilename = explode($separator, $input);
        $makeCamelCase = array_map(function ($value) {
            return ucfirst($value);
        }, $splitFilename);

        return implode("", $makeCamelCase);
    }
}
