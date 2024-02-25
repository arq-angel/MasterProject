<?php

declare(strict_types=1);

function dd(mixed $value) {

    // Increase the limits temporarily for var_dump
    ini_set('xdebug.var_display_max_children', 512);
    ini_set('xdebug.var_display_max_data', 2048);
    ini_set('xdebug.var_display_max_depth', 10);

    echo "<pre>";
    var_dump($value);
    echo "</pre>";
    die();
}
