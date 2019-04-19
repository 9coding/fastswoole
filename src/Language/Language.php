<?php

namespace Fastapi\Language;

use Fastapi\Core;

class Language {

    private $lang = [];

    public function __construct($lang) {
        $systemLang = LIB_DIR.'/Language/'.$lang.'.php';
        $userLang = APP_DIR.'/'.Core::$container['config']['default_module'].'/lang/'.$lang.'.php';
        foreach ([$systemLang,$userLang] as $value) {
            if (file_exists($value)) {
                $langArray = include $value;
                $this->lang = array_merge($this->lang, $langArray);
            }
        }
    }

    public function get($key) {
        if (isset($this->lang[$key]) && $this->lang[$key]) {
            return $this->lang[$key];
        } else {
            return $key;
        }
    }
}
