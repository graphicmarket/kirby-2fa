<?php

namespace graphicmarket\kirby2fa;

use Kirby\Cms\App;

@include_once __DIR__ . '/vendor/autoload.php';

load([
    'graphicmarket\\kirby2fa\\authenticator' => './src/models/Authenticator.php',
    'graphicmarket\\kirby2fa\\register' => './src/models/Register.php',
], __DIR__);

App::plugin('graphicmarket/kirby-2fa', [
    'options' => require 'src/config/options.php',
    'api' => require 'src/config/api.php',
    'fields' => require 'src/config/fields.php',
    'userMethods' => require 'src/config/userMethods.php',
]);