<?php namespace NozCore\Frontend;

class UrlManager {

    private $urls;

    public function __construct() {
        if (isset($GLOBALS['config']->frontend) && isset($GLOBALS['config']->frontend['urls'])) {
            $this->urls = (object) $GLOBALS['config']->frontend['urls'];
        }
    }

    public function getBaseUrl() {
        if(isset($this->urls->baseUrl)) {
            return $this->urls->baseUrl;
        }

        return null;
    }

    public function getRegistrationUrl() {
        if($this->getBaseUrl() && isset($this->urls->registration)) {
            return $this->getBaseUrl() . $this->urls->registration;
        }

        return null;
    }
}