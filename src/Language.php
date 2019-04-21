<?php

namespace FastSwoole;

use FastSwoole\Core;

class Language {

    private $lang = [];

    public function __construct() {
        $lang = Core::$app['config']->get('app.'.MODE.'.default_language');
        $userLang = APP_DIR.'/language/'.$lang.'.php';
        foreach ($userLang as $value) {
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
