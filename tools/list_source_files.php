<?php
require __DIR__ . '/../vendor/autoload.php';

use OpenApi\SourceFinder;

$dirs = [
    __DIR__ . '/../app/Http/Controllers',
    __DIR__ . '/../app/Swagger',
    __DIR__ . '/../app/Models',
];

$finder = new SourceFinder($dirs, [], '*.php');

foreach ($finder as $file) {
    echo $file->getRealPath() . PHP_EOL;
}
