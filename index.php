<?php

namespace graphicmarket\kirby2fa;

use Kirby\Cms\App;

@include_once __DIR__ . '/vendor/autoload.php';

App::plugin('graphicmarket/kirby-2fa', [
    'options' => require 'src/config/options.php',
    'api' => require 'src/config/api.php',
    'fields' => require 'src/config/fields.php',
    'userMethods' => require 'src/config/userMethods.php',
    'hooks' => require 'src/config/hooks.php',
]);