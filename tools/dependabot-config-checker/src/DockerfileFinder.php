<?php

declare(strict_types=1);

namespace Ramona\AutomationPlatformToolDependabotConfigChecker;

use function array_unique;
use function array_values;
use function assert;
use const DIRECTORY_SEPARATOR;
use function dirname;
use function in_array;
use RecursiveCallbackFilterIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use function Safe\realpath;
use SplFileInfo;
use function str_ends_with;
use function str_replace;
use function str_starts_with;

final class DockerfileFinder
{
    /**
     * @return list<string>
     */
    public static function find(): array
    {
        $projectRoot = realpath(dirname(__DIR__, 3)) . DIRECTORY_SEPARATOR;
        $directoryIterator = new RecursiveDirectoryIterator($projectRoot);
        $filterIterator = new RecursiveCallbackFilterIterator(
            $directoryIterator,
            function (SplFileInfo|string $file): bool {
                assert($file instanceof SplFileInfo);

                return !($file->isDir() && in_array($file->getBasename(), ['vendor', 'target', '.git'], true));
            }
        );
        $iterator = new RecursiveIteratorIterator($filterIterator);

        $results = [];
        foreach ($iterator as $item) {
            assert($item instanceof SplFileInfo);

            if (
                $item->getBasename() === 'Dockerfile'
                || str_starts_with($item->getBasename(), 'Dockerfile.')
                || str_ends_with($item->getBasename(), '.Dockerfile')

            ) {
                $results[] = str_replace([$projectRoot, DIRECTORY_SEPARATOR], ['', '/'], realpath($item->getPath())) . '/';
            }
        }

        return array_values(array_unique($results));
    }
}
