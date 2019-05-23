<?php namespace NozCore;

class Logger {
    /** @var Logger $logger */
    private $logger = null;

    /**
     * Logger constructor.
     */
    public function __construct() {
        $logger = new \Logger();
    }
}