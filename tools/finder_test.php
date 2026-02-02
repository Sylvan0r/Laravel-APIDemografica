<?php
require __DIR__ . '/../vendor/autoload.php';

$finder = (new \Symfony\Component\Finder\Finder())->files()->in(__DIR__ . '/../app/Swagger')->name('*.php')->sortByName();
foreach ($finder as $f) {
    echo $f->getRealPath() . PHP_EOL;
}
