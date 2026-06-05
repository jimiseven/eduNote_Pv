<?php

return [
    'name' => getenv('APP_NAME') ?: 'EduNote',
    'base_path' => dirname(__DIR__),
    'base_url' => getenv('APP_URL') ?: '',
];
