<?php namespace NozCore\Message;

class Error extends MessageUtil {

    public function properties() {
        $backtrace = debug_backtrace();
        $amount = count($backtrace);

        if(isset($backtrace[$amount - 2])) {
            $backtrace = $backtrace[$amount - 2];
        } else {
            $backtrace = end($backtrace);
        }

        return [
            'backtrace' => [
                'file' => $backtrace['file'],
                'line' => $backtrace['line']
            ]
        ];
    }
}