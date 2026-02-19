<?php

declare(strict_types=1);

namespace Matecat\EmojiParser;

class Emoji
{

    /** @var array<string, string> */
    private static array $chmap = [];

    /** @var array<string, string> */
    private static array $inverse_char_map = [];

    /**
     * Generate the char map
     */
    private static function generateMap(): void
    {
        if (empty(self::$chmap)) {
            /** @var array<string, string> $map */
            $map = include_once __DIR__ . '/chmap.php';
            self::$chmap = $map;
        }
    }

    /**
     * Generate the inverse char map
     */
    private static function generateReverseMap(): void
    {
        self::generateMap();

        if (empty(self::$inverse_char_map)) {
            self::$inverse_char_map = array_flip(self::$chmap);
        }
    }

    public static function toEntity(string $str): string
    {
        self::generateMap();
        $letters = preg_split('//u', $str, -1, PREG_SPLIT_NO_EMPTY);

        if ($letters === false) {
            // @codeCoverageIgnoreStart
            return $str;
            // @codeCoverageIgnoreEnd
        }

        foreach ($letters as $letter) {
            if (isset(self::$chmap[$letter])) {
                $str = str_replace($letter, self::$chmap[$letter], $str);
            }
        }

        return $str;
    }

    public static function toEmoji(string $str): string
    {
        self::generateReverseMap();
        preg_match_all('/&#[0-9a-fA-F]+;/', $str, $emoji_entity_list, PREG_PATTERN_ORDER);

        foreach ($emoji_entity_list[0] as $emoji_entity) {
            if (array_key_exists($emoji_entity, self::$inverse_char_map)) {
                $str = str_replace($emoji_entity, self::$inverse_char_map[$emoji_entity], $str);
            }
        }

        return $str;
    }

}