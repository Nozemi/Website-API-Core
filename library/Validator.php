<?php namespace NozCore;

class Validator {

    public function validateEndpoint($endpoint) {

        if(is_a($endpoint, 'NozCore\\Endpoint', true)) {
            return true;
        }

        return false;
    }
}