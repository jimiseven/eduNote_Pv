<?php

return [
    'name' => getenv('APP_NAME') ?: 'ColdBend',
    'base_path' => dirname(__DIR__),
    'base_url' => getenv('APP_URL') ?: '',
];
