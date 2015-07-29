<?php
/**
 * Autoload classes
 */
 namespace Trendwerk\PostTypeOrder;

spl_autoload_register(function ($class) {
    $length = strlen(__NAMESPACE__);

    if (strncmp(__NAMESPACE__, $class, $length) !== 0) {
        return;
    }

    $relative_class = substr($class, $length);

    $file = __DIR__ . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});
