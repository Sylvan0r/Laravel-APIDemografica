<?php
require __DIR__ . '/../vendor/autoload.php';

$openapi = \OpenApi\scan([__DIR__ . '/../app/Http/Controllers', __DIR__ . '/../app/Swagger', __DIR__ . '/../app/Models']);

$paths = $openapi->paths ?? [];

echo "Paths found: " . count($paths) . PHP_EOL;
foreach ($paths as $path) {
    echo " - " . ($path->path ?? '(no path)') . PHP_EOL;
}

// Also print top-level annotations counts
$all = iterator_to_array($openapi);
$counts = [];
foreach ($all as $ann) {
    $classname = (new \ReflectionClass($ann))->getShortName();
    if (!isset($counts[$classname])) $counts[$classname] = 0;
    $counts[$classname]++;
}

echo "\nAnnotation counts:\n";
foreach ($counts as $k => $v) {
    echo " - $k: $v\n";
}
