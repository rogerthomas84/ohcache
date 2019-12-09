<?php
$_SESSION = [];

$path = realpath(dirname(__FILE__) . '/../');

spl_autoload_register(function ($name) use($path) {
    $name = implode(DIRECTORY_SEPARATOR, explode('\\', $name)) . '.php';
    $srcPath = $path . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . $name;
    if (file_exists($srcPath)) {
        include $srcPath;
    }
    $testsPath = $path . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . $name;
    if (file_exists($testsPath)) {
        include $testsPath;
    }
});
