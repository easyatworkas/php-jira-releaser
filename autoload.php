<?php

spl_autoload_register(function ($class) {
    if (file_exists($file = __DIR__ . '/lib/' . str_replace('\\', '/', $class) . '.php')) {
        require($file);
    }
});
