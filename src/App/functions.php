<?php

declare(strict_types=1);

use Framework\Http;

function dd(mixed $value)
{
    // Increase the limits temporarily for var_dump
    ini_set('xdebug.var_display_max_children', 512);
    ini_set('xdebug.var_display_max_data', 2048);
    ini_set('xdebug.var_display_max_depth', 10);

    echo "<pre>";
    var_dump($value);
    echo "</pre>";
    die();
}

function e(mixed $value): string
{
    return htmlspecialchars((string) $value);
}

function redirectTo(string $path)
{
    header("Location: {$path}");
    http_response_code(Http::REDIRECT_STATUS_CODE);
    exit;
}

