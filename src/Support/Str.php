<?php

/*
 * This file is part of the Spress\Import.
 *
 * (c) YoSymfony <http://github.com/yosymfony>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spress\Import\Support;

/**
 * Utils for processing strings.
 *
 * @author Victor Puertas <vpgugr@gmail.com>
 */
class Str
{
    protected static $chars;

    /**
     * Generate a URL friendly "slug".
     *
     * @param string $separator
     *
     * @return string
     */
    public static function slug($str, $separator = '-')
    {
        $str = self::toAscii($str);

        $flip = $separator == '-' ? '_' : '-';
        $str = str_replace('.', $separator, $str);
        $str = preg_replace('!['.preg_quote($flip).']+!u', $separator, $str);
        $str = preg_replace('![^'.preg_quote($separator).'\pL\pN\s]+!u', '', mb_strtolower($str));
        $str = preg_replace('!['.preg_quote($separator).'\s]+!u', $separator, $str);

        return trim($str, $separator);
    }

    /**
     * Determine if a the string starts with a given substring.
     *
     * @param string $value
     *
     * @return bool
     */
    public static function startWith($str, $value)
    {
        return $value != '' && strpos($str, $value) === 0;
    }

    /**
     * Determine if a the string ends with a given substring.
     *
     * @param string $str   The string in which finds the sufix.
     * @param string $value
     *
     * @return bool
     */
    public static function endWith($str, $value)
    {
        return (string) $value === substr($str, -strlen($value));
    }

    /**
     * Deletes a prefix of the string.
     *
     * @param string $str    The string in which finds the prefix.
     * @param string $prefix The prefix.
     *
     * @return string The string without prefix.
     */
    public static function deletePrefix($str, $prefix)
    {
        if (self::startWith($str, $prefix) === true) {
            return substr($str, strlen($prefix));
        }

        return $str;
    }

    /**
     * Deletes a sufix of the string.
     *
     * @param string $str   The string in which finds the sufix.
     * @param string $sufix The sufix.
     *
     * @return string The string without sufix.
     */
    public static function deleteSufix($str, $sufix)
    {
        if (self::endWith($str, $sufix) === true) {
            return substr($str, 0, -strlen($sufix));
        }

        return $str;
    }

    /**
     * Transliterate a UTF-8 value to ASCII.
     *
     * @param string $str               The string to performs.
     * @param bool   $removeUnsupported Whether or not to remove the
     *                                  unsupported characters
     *
     * @return string
     */
    public static function toAscii($str, $removeUnsupported = true)
    {
        foreach (self::getChars() as $key => $value) {
            $str = str_replace($value, $key, $str);
        }
        if ($removeUnsupported) {
            $str = preg_replace('/[^\x20-\x7E]/u', '', $str);
        }

        return $str;
    }

    /**
     * Gets the conversion table.
     *
     * @return array
     */
    protected static function getChars()
    {
        if (is_null(self::$chars) == false) {
            return self::$chars;
        }

        self::$chars = [
            'a' => [
                        'à', 'á', 'ả', 'ã', 'ạ', 'ă', 'ắ', 'ằ', 'ẳ', 'ẵ',
                        'ặ', 'â', 'ấ', 'ầ', 'ẩ', 'ẫ', 'ậ', 'ä', 'ā', 'ą',
                        'å', 'α', 'ά', 'ἀ', 'ἁ', 'ἂ', 'ἃ', 'ἄ', 'ἅ', 'ἆ',
                        'ἇ', 'ᾀ', 'ᾁ', 'ᾂ', 'ᾃ', 'ᾄ', 'ᾅ', 'ᾆ', 'ᾇ', 'ὰ',
                        'ά', 'ᾰ', 'ᾱ', 'ᾲ', 'ᾳ', 'ᾴ', 'ᾶ', 'ᾷ', 'а', 'أ', ],
            'b' => ['б', 'β', 'Ъ', 'Ь', 'ب'],
            'c' => ['ç', 'ć', 'č', 'ĉ', 'ċ'],
            'd' => ['ď', 'ð', 'đ', 'ƌ', 'ȡ', 'ɖ', 'ɗ', 'ᵭ', 'ᶁ', 'ᶑ',
                        'д', 'δ', 'د', 'ض', ],
            'e' => ['é', 'è', 'ẻ', 'ẽ', 'ẹ', 'ê', 'ế', 'ề', 'ể', 'ễ',
                        'ệ', 'ë', 'ē', 'ę', 'ě', 'ĕ', 'ė', 'ε', 'έ', 'ἐ',
                        'ἑ', 'ἒ', 'ἓ', 'ἔ', 'ἕ', 'ὲ', 'έ', 'е', 'ё', 'э',
                        'є', 'ə', ],
            'f' => ['ф', 'φ', 'ف'],
            'g' => ['ĝ', 'ğ', 'ġ', 'ģ', 'г', 'ґ', 'γ', 'ج'],
            'h' => ['ĥ', 'ħ', 'η', 'ή', 'ح', 'ه'],
            'i' => ['í', 'ì', 'ỉ', 'ĩ', 'ị', 'î', 'ï', 'ī', 'ĭ', 'į',
                            'ı', 'ι', 'ί', 'ϊ', 'ΐ', 'ἰ', 'ἱ', 'ἲ', 'ἳ', 'ἴ',
                            'ἵ', 'ἶ', 'ἷ', 'ὶ', 'ί', 'ῐ', 'ῑ', 'ῒ', 'ΐ', 'ῖ',
                            'ῗ', 'і', 'ї', 'и', ],
            'j' => ['ĵ', 'ј', 'Ј'],
            'k' => ['ķ', 'ĸ', 'к', 'κ', 'Ķ', 'ق', 'ك'],
            'l' => ['ł', 'ľ', 'ĺ', 'ļ', 'ŀ', 'л', 'λ', 'ل'],
            'm' => ['м', 'μ', 'م'],
            'n' => ['ñ', 'ń', 'ň', 'ņ', 'ŉ', 'ŋ', 'ν', 'н', 'ن'],
            'o' => ['ó', 'ò', 'ỏ', 'õ', 'ọ', 'ô', 'ố', 'ồ', 'ổ', 'ỗ',
                            'ộ', 'ơ', 'ớ', 'ờ', 'ở', 'ỡ', 'ợ', 'ø', 'ō', 'ő',
                            'ŏ', 'ο', 'ὀ', 'ὁ', 'ὂ', 'ὃ', 'ὄ', 'ὅ', 'ὸ', 'ό',
                            'ö', 'о', 'و', 'θ', ],
            'p' => ['п', 'π'],
            'r' => ['ŕ', 'ř', 'ŗ', 'р', 'ρ', 'ر'],
            's' => ['ś', 'š', 'ş', 'с', 'σ', 'ș', 'ς', 'س', 'ص'],
            't' => ['ť', 'ţ', 'т', 'τ', 'ț', 'ت', 'ط'],
            'u' => ['ú', 'ù', 'ủ', 'ũ', 'ụ', 'ư', 'ứ', 'ừ', 'ử', 'ữ',
                            'ự', 'ü', 'û', 'ū', 'ů', 'ű', 'ŭ', 'ų', 'µ', 'у', ],
            'v' => ['в'],
            'w' => ['ŵ', 'ω', 'ώ'],
            'x' => ['χ'],
            'y' => ['ý', 'ỳ', 'ỷ', 'ỹ', 'ỵ', 'ÿ', 'ŷ', 'й', 'ы', 'υ',
                            'ϋ', 'ύ', 'ΰ', 'ي', ],
            'z' => ['ź', 'ž', 'ż', 'з', 'ζ', 'ز'],
            'aa' => ['ع'],
            'ae' => ['æ'],
            'ch' => ['ч'],
            'dj' => ['ђ', 'đ'],
            'dz' => ['џ'],
            'gh' => ['غ'],
            'kh' => ['х', 'خ'],
            'lj' => ['љ'],
            'nj' => ['њ'],
            'oe' => ['œ'],
            'ps' => ['ψ'],
            'sh' => ['ш'],
            'shch' => ['щ'],
            'ss' => ['ß'],
            'th' => ['þ', 'ث', 'ذ', 'ظ'],
            'ts' => ['ц'],
            'ya' => ['я'],
            'yu' => ['ю'],
            'zh' => ['ж'],
            '(c)' => ['©'],
            'A' => ['Á', 'À', 'Ả', 'Ã', 'Ạ', 'Ă', 'Ắ', 'Ằ', 'Ẳ', 'Ẵ',
                            'Ặ', 'Â', 'Ấ', 'Ầ', 'Ẩ', 'Ẫ', 'Ậ', 'Ä', 'Å', 'Ā',
                            'Ą', 'Α', 'Ά', 'Ἀ', 'Ἁ', 'Ἂ', 'Ἃ', 'Ἄ', 'Ἅ', 'Ἆ',
                            'Ἇ', 'ᾈ', 'ᾉ', 'ᾊ', 'ᾋ', 'ᾌ', 'ᾍ', 'ᾎ', 'ᾏ', 'Ᾰ',
                            'Ᾱ', 'Ὰ', 'Ά', 'ᾼ', 'А', ],
            'B' => ['Б', 'Β'],
            'C' => ['Ç', 'Ć', 'Č', 'Ĉ', 'Ċ'],
            'D' => ['Ď', 'Ð', 'Đ', 'Ɖ', 'Ɗ', 'Ƌ', 'ᴅ', 'ᴆ', 'Д', 'Δ'],
            'E' => ['É', 'È', 'Ẻ', 'Ẽ', 'Ẹ', 'Ê', 'Ế', 'Ề', 'Ể', 'Ễ',
                            'Ệ', 'Ë', 'Ē', 'Ę', 'Ě', 'Ĕ', 'Ė', 'Ε', 'Έ', 'Ἐ',
                            'Ἑ', 'Ἒ', 'Ἓ', 'Ἔ', 'Ἕ', 'Έ', 'Ὲ', 'Е', 'Ё', 'Э',
                            'Є', 'Ə', ],
            'F' => ['Ф', 'Φ'],
            'G' => ['Ğ', 'Ġ', 'Ģ', 'Г', 'Ґ', 'Γ'],
            'H' => ['Η', 'Ή'],
            'I' => ['Í', 'Ì', 'Ỉ', 'Ĩ', 'Ị', 'Î', 'Ï', 'Ī', 'Ĭ', 'Į',
                            'İ', 'Ι', 'Ί', 'Ϊ', 'Ἰ', 'Ἱ', 'Ἳ', 'Ἴ', 'Ἵ', 'Ἶ',
                            'Ἷ', 'Ῐ', 'Ῑ', 'Ὶ', 'Ί', 'И', 'І', 'Ї', ],
            'K' => ['К', 'Κ'],
            'L' => ['Ĺ', 'Ł', 'Л', 'Λ', 'Ļ'],
            'M' => ['М', 'Μ'],
            'N' => ['Ń', 'Ñ', 'Ň', 'Ņ', 'Ŋ', 'Н', 'Ν'],
            'O' => ['Ó', 'Ò', 'Ỏ', 'Õ', 'Ọ', 'Ô', 'Ố', 'Ồ', 'Ổ', 'Ỗ',
                            'Ộ', 'Ơ', 'Ớ', 'Ờ', 'Ở', 'Ỡ', 'Ợ', 'Ö', 'Ø', 'Ō',
                            'Ő', 'Ŏ', 'Ο', 'Ό', 'Ὀ', 'Ὁ', 'Ὂ', 'Ὃ', 'Ὄ', 'Ὅ',
                            'Ὸ', 'Ό', 'О', 'Θ', 'Ө', ],
            'P' => ['П', 'Π'],
            'R' => ['Ř', 'Ŕ', 'Р', 'Ρ'],
            'S' => ['Ş', 'Ŝ', 'Ș', 'Š', 'Ś', 'С', 'Σ'],
            'T' => ['Ť', 'Ţ', 'Ŧ', 'Ț', 'Т', 'Τ'],
            'U' => ['Ú', 'Ù', 'Ủ', 'Ũ', 'Ụ', 'Ư', 'Ứ', 'Ừ', 'Ử', 'Ữ',
                            'Ự', 'Û', 'Ü', 'Ū', 'Ů', 'Ű', 'Ŭ', 'Ų', 'У', ],
            'V' => ['В'],
            'W' => ['Ω', 'Ώ'],
            'X' => ['Χ'],
            'Y' => ['Ý', 'Ỳ', 'Ỷ', 'Ỹ', 'Ỵ', 'Ÿ', 'Ῠ', 'Ῡ', 'Ὺ', 'Ύ',
                            'Ы', 'Й', 'Υ', 'Ϋ', ],
            'Z' => ['Ź', 'Ž', 'Ż', 'З', 'Ζ'],
            'AE' => ['Æ'],
            'CH' => ['Ч'],
            'DJ' => ['Ђ'],
            'DZ' => ['Џ'],
            'KH' => ['Х'],
            'LJ' => ['Љ'],
            'NJ' => ['Њ'],
            'PS' => ['Ψ'],
            'SH' => ['Ш'],
            'SHCH' => ['Щ'],
            'SS' => ['ẞ'],
            'TH' => ['Þ'],
            'TS' => ['Ц'],
            'YA' => ['Я'],
            'YU' => ['Ю'],
            'ZH' => ['Ж'],
            ' ' => ["\xC2\xA0", "\xE2\x80\x80", "\xE2\x80\x81",
                            "\xE2\x80\x82", "\xE2\x80\x83", "\xE2\x80\x84",
                            "\xE2\x80\x85", "\xE2\x80\x86", "\xE2\x80\x87",
                            "\xE2\x80\x88", "\xE2\x80\x89", "\xE2\x80\x8A",
                            "\xE2\x80\xAF", "\xE2\x81\x9F", "\xE3\x80\x80", ],
        ];

        return self::$chars;
    }
}
