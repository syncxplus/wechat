<?php

require_once __DIR__ . '/index.php';

$dir = ROOT . '/html/data/uploads/';
$list = scandir($dir);
array_shift($list); //remove .
array_shift($list); //remove ..
foreach ($list as $file) {
    echo $file, PHP_EOL;
    if (substr($file, 0, 1) == '_') {
        rename($dir . $file, $dir . substr($file, 1));
    }
}

echo 'Finish', PHP_EOL;