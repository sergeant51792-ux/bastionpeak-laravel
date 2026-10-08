<?php
require 'vendor/autoload.php';
$r = new ReflectionClass('Illuminate\Http\Request');
$methods = $r->getMethods();
foreach ($methods as $m) {
    if (stripos($m->getName(), 'user') !== false) {
        echo $m->getName() . PHP_EOL;
    }
}
