<?php
foreach (new DirectoryIterator(__DIR__ . '/../app/Swagger') as $fileinfo) {
    if ($fileinfo->isDot()) continue;
    echo $fileinfo->getFilename() . PHP_EOL;
    echo '  realpath: ' . $fileinfo->getRealPath() . PHP_EOL;
    echo '  isFile: ' . ($fileinfo->isFile() ? 'yes' : 'no') . PHP_EOL;
    echo '  filesize: ' . $fileinfo->getSize() . PHP_EOL;
    echo '  mtime: ' . date('c', $fileinfo->getMTime()) . PHP_EOL;
    echo '  readable: ' . (is_readable($fileinfo->getRealPath()) ? 'yes' : 'no') . PHP_EOL;
}
