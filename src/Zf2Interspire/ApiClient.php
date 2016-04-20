<?php

namespace Zf2Interspire;

class ApiClient extends Configurable {

    public $apiToken;
    public $apiUser;
    public $host;
    public $endPoint;

    public function __call() {
        # do magic
    }
}