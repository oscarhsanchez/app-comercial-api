<?php

class APIexception extends Exception {
    protected $data;

    public function __construct($message, $code = 600, $data = null) {
        $this->data = $data;
        parent::__construct($message, $code);
    }

    public function __toString() {
        return "exception[{$this->code}]: {$this->message}" . PHP_EOL . $this->getdata() . PHP_EOL;
    }

    public function getData() {
        if (stripos($this->data, '<b>description</b> <u>') !== false) {
            $data = explode('<b>description</b> <u>', $this->data);
            $data = explode('</u></p>', $data[1]);
            $data = $data[0];
        } else {
            $data = $this->data;
        }
        
        return $data;
    }
}


?>
