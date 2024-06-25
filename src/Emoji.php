<?php

namespace Matecat\EmojiParser;

class Emoji {

    /**
     * Note: for not visible characters:
     * Launch IDE debug, and evaluate the expression:
     *
     * html_entity_decode("xxxx");
     *
     * and then copy the value
     *
     * @var array
     */
    private static $chmap = [];
    private static $inverse_char_map = [];

    private static function generateMap() {
        if ( empty( self::$chmap ) ) {
            self::$chmap = include_once __DIR__ .'/../config/chmap.php';
        }
    }

    private static function generateReverseMap() {
        self::generateMap();

        if ( empty( self::$inverse_char_map ) ) {
            self::$inverse_char_map = array_flip( self::$chmap );
        }
    }

    /**
     * @param $str
     *
     * @return string
     */
    public static function toEntity( $str ) {
        self::generateMap();
        $letters = preg_split( '//u', $str, null, PREG_SPLIT_NO_EMPTY );

        foreach ( $letters as $letter ) {
            if ( isset ( self::$chmap[ $letter ] ) ) {
                $str = str_replace( $letter, self::$chmap[ $letter ], $str );
            }
        }

        return $str;
    }

    /**
     * @param $str
     *
     * @return string
     */
    public static function toEmoji( $str ) {
        self::generateReverseMap();
        preg_match_all( '/&#[0-9a-fA-F]+;/', $str, $emoji_entity_list, PREG_PATTERN_ORDER );

        foreach ( $emoji_entity_list[ 0 ] as $emoji_entity ) {
            if ( array_key_exists( $emoji_entity, self::$inverse_char_map ) ) {
                $str = str_replace( $emoji_entity, self::$inverse_char_map[ $emoji_entity ], $str );
            }
        }

        return $str;
    }

}