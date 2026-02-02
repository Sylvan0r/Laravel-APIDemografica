<?php
require __DIR__ . '/../vendor/autoload.php';

use OpenApi\Generator;
use OpenApi\SourceFinder;

try {
    $generator = new Generator();
    $generator->setAnalyser(new \OpenApi\Analysers\ReflectionAnalyser([
        new \OpenApi\Analysers\AttributeAnnotationFactory(),
        new \OpenApi\Analysers\DocBlockAnnotationFactory(),
    ]));

    $finder = new SourceFinder([
        __DIR__ . '/../app/Swagger',
        __DIR__ . '/../app/Http/Controllers',
        __DIR__ . '/../app/Models',
    ], [], '*.php');

    $openapi = $generator->generate($finder);

    echo 'Info: ' . (isset($openapi->info) ? get_class($openapi->info) : '(none)') . PHP_EOL;
    var_dump($openapi->info ?? null);
    echo "\nTop-level counts:\n";
    $all = iterator_to_array($openapi);
    $counts = [];
    foreach ($all as $ann) {
        $classname = (new ReflectionClass($ann))->getShortName();
        $counts[$classname] = ($counts[$classname] ?? 0) + 1;
    }
    foreach ($counts as $k => $v) echo " - $k: $v\n";
} catch (Throwable $e) {
    echo "Exception: " . get_class($e) . ': ' . $e->getMessage() . PHP_EOL;
    echo $e->getTraceAsString();
}
