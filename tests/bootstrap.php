<?php

require __DIR__ . '/../vendor/autoload.php';

$loader = require __DIR__ . '/../vendor/autoload.php';
$loader->add('Tests\\', __DIR__);
$loader->setUseIncludePath(true);

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
