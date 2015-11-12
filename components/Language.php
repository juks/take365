<?php

namespace app\components;

use YII;

class Language {
    static protected $_knownLanguages = [
        1 => ['id' => 1, 'code' => 'aa', 'title' => 'Afar'],
        2 => ['id' => 2, 'code' => 'ab', 'title' => 'Abkhazian'],
        3 => ['id' => 3, 'code' => 'ae', 'title' => 'Avestan'],
        4 => ['id' => 4, 'code' => 'af', 'title' => 'Afrikaans'],
        5 => ['id' => 5, 'code' => 'ak', 'title' => 'Akan'],
        6 => ['id' => 6, 'code' => 'am', 'title' => 'Amharic'],
        7 => ['id' => 7, 'code' => 'an', 'title' => 'Aragonese'],
        8 => ['id' => 8, 'code' => 'ar', 'title' => 'Arabic'],
        9 => ['id' => 9, 'code' => 'as', 'title' => 'Assamese'],
        10 => ['id' => 10, 'code' => 'av', 'title' => 'Avaric'],
        11 => ['id' => 11, 'code' => 'ay', 'title' => 'Aymara'],
        12 => ['id' => 12, 'code' => 'az', 'title' => 'Azerbaijani'],
        13 => ['id' => 13, 'code' => 'ba', 'title' => 'Bashkir'],
        14 => ['id' => 14, 'code' => 'be', 'title' => 'Belarusian'],
        15 => ['id' => 15, 'code' => 'bg', 'title' => 'Bulgarian'],
        16 => ['id' => 16, 'code' => 'bh', 'title' => 'Collective'],
        17 => ['id' => 17, 'code' => 'bi', 'title' => 'Bislama'],
        18 => ['id' => 18, 'code' => 'bm', 'title' => 'Bambara'],
        19 => ['id' => 19, 'code' => 'bn', 'title' => 'Bengali'],
        20 => ['id' => 20, 'code' => 'bo', 'title' => 'Tibetan'],
        21 => ['id' => 21, 'code' => 'br', 'title' => 'Breton'],
        22 => ['id' => 22, 'code' => 'bs', 'title' => 'Bosnian'],
        23 => ['id' => 23, 'code' => 'ca', 'title' => 'Catalan'],
        24 => ['id' => 24, 'code' => 'ce', 'title' => 'Chechen'],
        25 => ['id' => 25, 'code' => 'ch', 'title' => 'Chamorro'],
        26 => ['id' => 26, 'code' => 'co', 'title' => 'Corsican'],
        27 => ['id' => 27, 'code' => 'cr', 'title' => 'Cree'],
        28 => ['id' => 28, 'code' => 'cs', 'title' => 'Czech'],
        29 => ['id' => 29, 'code' => 'cu', 'title' => 'Church Slavic'],
        30 => ['id' => 30, 'code' => 'cv', 'title' => 'Chuvash'],
        31 => ['id' => 31, 'code' => 'cy', 'title' => 'Welsh'],
        32 => ['id' => 32, 'code' => 'da', 'title' => 'Danish'],
        33 => ['id' => 33, 'code' => 'de', 'title' => 'German'],
        34 => ['id' => 34, 'code' => 'dv', 'title' => 'Dhivehi'],
        35 => ['id' => 35, 'code' => 'dz', 'title' => 'Dzongkha'],
        36 => ['id' => 36, 'code' => 'ee', 'title' => 'Ewe'],
        37 => ['id' => 37, 'code' => 'el', 'title' => 'Modern Greek'],
        38 => ['id' => 38, 'code' => 'en', 'title' => 'English', 'titleEnable' => 'In English'],
        39 => ['id' => 39, 'code' => 'eo', 'title' => 'Esperanto'],
        40 => ['id' => 40, 'code' => 'es', 'title' => 'Spanish'],
        41 => ['id' => 41, 'code' => 'et', 'title' => 'Estonian'],
        42 => ['id' => 42, 'code' => 'eu', 'title' => 'Basque'],
        43 => ['id' => 43, 'code' => 'fa', 'title' => 'Persian'],
        44 => ['id' => 44, 'code' => 'ff', 'title' => 'Fulah'],
        45 => ['id' => 45, 'code' => 'fi', 'title' => 'Finnish'],
        46 => ['id' => 46, 'code' => 'fj', 'title' => 'Fijian'],
        47 => ['id' => 47, 'code' => 'fo', 'title' => 'Faroese'],
        48 => ['id' => 48, 'code' => 'fr', 'title' => 'French'],
        49 => ['id' => 49, 'code' => 'fy', 'title' => 'Western Frisian'],
        50 => ['id' => 50, 'code' => 'ga', 'title' => 'Irish'],
        51 => ['id' => 51, 'code' => 'gd', 'title' => 'Scottish Gaelic'],
        52 => ['id' => 52, 'code' => 'gl', 'title' => 'Galician'],
        53 => ['id' => 53, 'code' => 'gn', 'title' => 'Guarani'],
        54 => ['id' => 54, 'code' => 'gu', 'title' => 'Gujarati'],
        55 => ['id' => 55, 'code' => 'gv', 'title' => 'Manx'],
        56 => ['id' => 56, 'code' => 'ha', 'title' => 'Hausa'],
        57 => ['id' => 57, 'code' => 'he', 'title' => 'Hebrew'],
        58 => ['id' => 58, 'code' => 'hi', 'title' => 'Hindi'],
        59 => ['id' => 59, 'code' => 'ho', 'title' => 'Hiri Motu'],
        60 => ['id' => 60, 'code' => 'hr', 'title' => 'Croatian'],
        61 => ['id' => 61, 'code' => 'ht', 'title' => 'Haitian'],
        62 => ['id' => 62, 'code' => 'hu', 'title' => 'Hungarian'],
        63 => ['id' => 63, 'code' => 'hy', 'title' => 'Armenian'],
        64 => ['id' => 64, 'code' => 'hz', 'title' => 'Herero'],
        65 => ['id' => 65, 'code' => 'ia', 'title' => 'Interlingua'],
        66 => ['id' => 66, 'code' => 'id', 'title' => 'Indonesian'],
        67 => ['id' => 67, 'code' => 'ie', 'title' => 'Interlingue'],
        68 => ['id' => 68, 'code' => 'ig', 'title' => 'Igbo'],
        69 => ['id' => 69, 'code' => 'ii', 'title' => 'Sichuan Yi'],
        70 => ['id' => 70, 'code' => 'ik', 'title' => 'Inupiaq'],
        71 => ['id' => 71, 'code' => 'io', 'title' => 'Ido'],
        72 => ['id' => 72, 'code' => 'is', 'title' => 'Icelandic'],
        73 => ['id' => 73, 'code' => 'it', 'title' => 'Italian'],
        74 => ['id' => 74, 'code' => 'iu', 'title' => 'Inuktitut'],
        75 => ['id' => 75, 'code' => 'ja', 'title' => 'Japanese'],
        76 => ['id' => 76, 'code' => 'jv', 'title' => 'Javanese'],
        77 => ['id' => 77, 'code' => 'ka', 'title' => 'Georgian'],
        78 => ['id' => 78, 'code' => 'kg', 'title' => 'Kongo'],
        79 => ['id' => 79, 'code' => 'ki', 'title' => 'Kikuyu'],
        80 => ['id' => 80, 'code' => 'kj', 'title' => 'Kuanyama'],
        81 => ['id' => 81, 'code' => 'kk', 'title' => 'Kazakh'],
        82 => ['id' => 82, 'code' => 'kl', 'title' => 'Kalaallisut'],
        83 => ['id' => 83, 'code' => 'km', 'title' => 'Central Khmer'],
        84 => ['id' => 84, 'code' => 'kn', 'title' => 'Kannada'],
        85 => ['id' => 85, 'code' => 'ko', 'title' => 'Korean'],
        86 => ['id' => 86, 'code' => 'kr', 'title' => 'Kanuri'],
        87 => ['id' => 87, 'code' => 'ks', 'title' => 'Kashmiri'],
        88 => ['id' => 88, 'code' => 'ku', 'title' => 'Kurdish'],
        89 => ['id' => 89, 'code' => 'kv', 'title' => 'Komi'],
        90 => ['id' => 90, 'code' => 'kw', 'title' => 'Cornish'],
        91 => ['id' => 91, 'code' => 'ky', 'title' => 'Kirghiz'],
        92 => ['id' => 92, 'code' => 'la', 'title' => 'Latin'],
        93 => ['id' => 93, 'code' => 'lb', 'title' => 'Luxembourgish'],
        94 => ['id' => 94, 'code' => 'lg', 'title' => 'Ganda'],
        95 => ['id' => 95, 'code' => 'li', 'title' => 'Limburgan'],
        96 => ['id' => 96, 'code' => 'ln', 'title' => 'Lingala'],
        97 => ['id' => 97, 'code' => 'lo', 'title' => 'Lao'],
        98 => ['id' => 98, 'code' => 'lt', 'title' => 'Lithuanian'],
        99 => ['id' => 99, 'code' => 'lu', 'title' => 'Luba'],
        100 => ['id' => 100, 'code' => 'lv', 'title' => 'Latvian'],
        101 => ['id' => 101, 'code' => 'mg', 'title' => 'Malagasy'],
        102 => ['id' => 102, 'code' => 'mh', 'title' => 'Marshallese'],
        103 => ['id' => 103, 'code' => 'mi', 'title' => 'Maori'],
        104 => ['id' => 104, 'code' => 'mk', 'title' => 'Macedonian'],
        105 => ['id' => 105, 'code' => 'ml', 'title' => 'Malayalam'],
        106 => ['id' => 106, 'code' => 'mn', 'title' => 'Mongolian'],
        107 => ['id' => 107, 'code' => 'mr', 'title' => 'Marathi'],
        108 => ['id' => 108, 'code' => 'ms', 'title' => 'Malay'],
        109 => ['id' => 109, 'code' => 'mt', 'title' => 'Maltese'],
        110 => ['id' => 110, 'code' => 'my', 'title' => 'Burmese'],
        111 => ['id' => 111, 'code' => 'na', 'title' => 'Nauru'],
        112 => ['id' => 112, 'code' => 'nb', 'title' => 'Norwegian Bokm'],
        113 => ['id' => 113, 'code' => 'nd', 'title' => 'North Ndebele'],
        114 => ['id' => 114, 'code' => 'ne', 'title' => 'Nepali'],
        115 => ['id' => 115, 'code' => 'ng', 'title' => 'Ndonga'],
        116 => ['id' => 116, 'code' => 'nl', 'title' => 'Dutch'],
        117 => ['id' => 117, 'code' => 'nn', 'title' => 'Norwegian Nynorsk'],
        118 => ['id' => 118, 'code' => 'no', 'title' => 'Norwegian'],
        119 => ['id' => 119, 'code' => 'nr', 'title' => 'South Ndebele'],
        120 => ['id' => 120, 'code' => 'nv', 'title' => 'Navajo'],
        121 => ['id' => 121, 'code' => 'ny', 'title' => 'Nyanja'],
        122 => ['id' => 122, 'code' => 'oc', 'title' => 'Occitan'],
        123 => ['id' => 123, 'code' => 'oj', 'title' => 'Ojibwa'],
        124 => ['id' => 124, 'code' => 'om', 'title' => 'Oromo'],
        125 => ['id' => 125, 'code' => 'or', 'title' => 'Oriya'],
        126 => ['id' => 126, 'code' => 'os', 'title' => 'Ossetian'],
        127 => ['id' => 127, 'code' => 'pa', 'title' => 'Panjabi'],
        128 => ['id' => 128, 'code' => 'pi', 'title' => 'Pali'],
        129 => ['id' => 129, 'code' => 'pl', 'title' => 'Polish'],
        130 => ['id' => 130, 'code' => 'ps', 'title' => 'Pushto'],
        131 => ['id' => 131, 'code' => 'pt', 'title' => 'Portuguese'],
        132 => ['id' => 132, 'code' => 'qu', 'title' => 'Quechua'],
        133 => ['id' => 133, 'code' => 'rm', 'title' => 'Romansh'],
        134 => ['id' => 134, 'code' => 'rn', 'title' => 'Rundi'],
        135 => ['id' => 135, 'code' => 'ro', 'title' => 'Romanian'],
        136 => ['id' => 136, 'code' => 'ru', 'title' => 'Russian', 'titleEnable' => 'По-русски'],
        137 => ['id' => 137, 'code' => 'rw', 'title' => 'Kinyarwanda'],
        138 => ['id' => 138, 'code' => 'sa', 'title' => 'Sanskrit'],
        139 => ['id' => 139, 'code' => 'sc', 'title' => 'Sardinian'],
        140 => ['id' => 140, 'code' => 'sd', 'title' => 'Sindhi'],
        141 => ['id' => 141, 'code' => 'se', 'title' => 'Northern Sami'],
        142 => ['id' => 142, 'code' => 'sg', 'title' => 'Sango'],
        143 => ['id' => 143, 'code' => 'si', 'title' => 'Sinhala'],
        144 => ['id' => 144, 'code' => 'sk', 'title' => 'Slovak'],
        145 => ['id' => 145, 'code' => 'sl', 'title' => 'Slovenian'],
        146 => ['id' => 146, 'code' => 'sm', 'title' => 'Samoan'],
        147 => ['id' => 147, 'code' => 'sn', 'title' => 'Shona'],
        148 => ['id' => 148, 'code' => 'so', 'title' => 'Somali'],
        149 => ['id' => 149, 'code' => 'sq', 'title' => 'Albanian'],
        150 => ['id' => 150, 'code' => 'sr', 'title' => 'Serbian'],
        151 => ['id' => 151, 'code' => 'ss', 'title' => 'Swati'],
        152 => ['id' => 152, 'code' => 'st', 'title' => 'Southern Sotho'],
        153 => ['id' => 153, 'code' => 'su', 'title' => 'Sundanese'],
        154 => ['id' => 154, 'code' => 'sv', 'title' => 'Swedish'],
        155 => ['id' => 155, 'code' => 'sw', 'title' => 'Swahili'],
        156 => ['id' => 156, 'code' => 'ta', 'title' => 'Tamil'],
        157 => ['id' => 157, 'code' => 'te', 'title' => 'Telugu'],
        158 => ['id' => 158, 'code' => 'tg', 'title' => 'Tajik'],
        159 => ['id' => 159, 'code' => 'th', 'title' => 'Thai'],
        160 => ['id' => 160, 'code' => 'ti', 'title' => 'Tigrinya'],
        161 => ['id' => 161, 'code' => 'tk', 'title' => 'Turkmen'],
        162 => ['id' => 162, 'code' => 'tl', 'title' => 'Tagalog'],
        163 => ['id' => 163, 'code' => 'tn', 'title' => 'Tswana'],
        164 => ['id' => 164, 'code' => 'to', 'title' => 'Tonga'],
        165 => ['id' => 165, 'code' => 'tr', 'title' => 'Turkish'],
        166 => ['id' => 166, 'code' => 'ts', 'title' => 'Tsonga'],
        167 => ['id' => 167, 'code' => 'tt', 'title' => 'Tatar'],
        168 => ['id' => 168, 'code' => 'tw', 'title' => 'Twi'],
        169 => ['id' => 169, 'code' => 'ty', 'title' => 'Tahitian'],
        170 => ['id' => 170, 'code' => 'ug', 'title' => 'Uighur'],
        171 => ['id' => 171, 'code' => 'uk', 'title' => 'Ukrainian'],
        172 => ['id' => 172, 'code' => 'ur', 'title' => 'Urdu'],
        173 => ['id' => 173, 'code' => 'uz', 'title' => 'Uzbek'],
        174 => ['id' => 174, 'code' => 've', 'title' => 'Venda'],
        175 => ['id' => 175, 'code' => 'vi', 'title' => 'Vietnamese'],
        176 => ['id' => 176, 'code' => 'vo', 'title' => 'Volap'],
        177 => ['id' => 177, 'code' => 'wa', 'title' => 'Walloon'],
        178 => ['id' => 178, 'code' => 'wo', 'title' => 'Wolof'],
        179 => ['id' => 179, 'code' => 'xh', 'title' => 'Xhosa'],
        180 => ['id' => 180, 'code' => 'yi', 'title' => 'Yiddish'],
        181 => ['id' => 181, 'code' => 'yo', 'title' => 'Yoruba'],
        182 => ['id' => 182, 'code' => 'za', 'title' => 'Zhuang'],
        183 => ['id' => 183, 'code' => 'zh', 'title' => 'Chinese'],
        184 => ['id' => 184, 'code' => 'zu', 'title' => 'Zulu']
    ];

    public static $defaultPanelLanguage = 'en';

    public static $supportedLanguages = [
        ['code' => 'en', 'title' => 'English'],
        ['code' => 'ru', 'title' => 'Русский']
    ];

    public static $monthShort = [
        38  => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        136 => ['id' => 136, 'Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек']
    ];

    /**
     * Returns default language
     *
     * @static
     * @return int
     */
    public static function getDefaultLanguageId() {
        return 38;
    }

    /**
     * Returns language by id
     * @static
     * @param $id
     * @return null
     */
    public static function getById($id) {
        return !empty(self::$_knownLanguages[$id]) ? self::$_knownLanguages[$id] : null;
    }

    /**
     * Returns language field value, where language is pecified by id
     * @static
     * @param $id
     * @param $field
     * @return string
     */
    public static function getFieldById($id, $field) {
        return !empty(self::$_knownLanguages[$id][$field]) ? self::$_knownLanguages[$id][$field] : '';
    }

    /**
     * Returns the language code specified by language id
     * @param $id
     * @return string
     */
    public static function getCodeById($id) {
        return self::getFieldById($id, 'code');
    }

    /**
     * Returns valid languages list
     *
     * @static
     * @param null $extra
     * @return array
     */
    public static function getValidLanguages($extra = null) {
        return self::$_knownLanguages;
    }

    /**
     * Returns language id by it's code
     * @static
     * @param $langCode
     * @return null
     */
    public static function getId($langCode) {
        $langCode = strtolower($langCode);

        foreach(self::$_knownLanguages as $id=>$lang) {
            if ($lang['code'] == $langCode) return $id;
        }

        return null;
    }

    /**
     * Checks if language id is valid
     * @static
     * @param $langId
     * @return bool
     */
    public static function isValidLanguage($langId) {
        return isset(self::$_knownLanguages[$langId]) ? self::$_knownLanguages[$langId] : null;
    }

    public static function getUserPreferredLanguage($extra = []) {
     
    }

    public static function detect() {
        $r = [];
        foreach (self::$supportedLanguages as $lang) {
            $r[] = $lang['code'];
        }
        return Yii::$app->request->getPreferredLanguage($r);
    }



    public static function storeCookieLang($value) {
        Yii::app()->request->cookies['cookie_name'] = new CHttpCookie('pmwLanguage', $value, ['expire' => time() + 86400 * 365]);
    }

    public static function getCookieLang() {
        return Yii::app()->request->cookies['pmwLanguage'] ? Yii::app()->request->cookies['pmwLanguage']->value : null;
    }
}
