<?php
// app/Config/Hardware.php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Hardware extends BaseConfig
{
    public string $apiUrl;
    public string $apiToken;

    public function __construct()
    {
        parent::__construct();

        $this->apiUrl   = env('hardware.apiUrl', '');
        $this->apiToken = env('hardware.apiToken', '');
    }
}