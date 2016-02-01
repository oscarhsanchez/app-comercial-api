<?php

class esocialException extends Exception {
    protected $msg;

    public function __construct($message, $code = 600, $msg = null) {
        $this->msg = $msg;
        parent::__construct($message, $code);
    }

    public function __toString() {
        return "exception[{$this->code}]: {$this->message}" . PHP_EOL . $this->getmsg() . PHP_EOL;
    }

    public function getmsg() {
        if (stripos($this->msg, '<b>description</b> <u>') !== false) {
            $msg = explode('<b>description</b> <u>', $this->msg);
            $msg = explode('</u></p>', $msg[1]);
            $msg = $msg[0];
        } else {
            $msg = $this->msg;
        }
        
        return $msg;
    }
}

