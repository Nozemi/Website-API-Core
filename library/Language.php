<?php namespace NozCore;

class Language {

    private $activeLanguage = 'en_us';

    public function __construct() {

        if(isset($_REQUEST['language'])) {
            $this->activeLanguage = $_REQUEST['language'];
        }
    }
}