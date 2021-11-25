<?php

declare(strict_types=1);

namespace Ramona\AutomationPlatformLibBuild\Definition;

use Closure;
use const DIRECTORY_SEPARATOR;
use Ramona\AutomationPlatformLibBuild\InvalidBuildDefinition;
use Ramona\AutomationPlatformLibBuild\Target;
use Ramona\AutomationPlatformLibBuild\TargetId;
use function Safe\realpath;

final class DefaultBuildDefinitionsLoader implements BuildDefinitionsLoader
{
    /** @var array<string, BuildDefinition> */
    private array $definitions = [];

    private function load(string $path): void
    {
        $path = realpath($path);
        /** @psalm-suppress UnresolvableInclude */
        $buildDefinition = require $path . DIRECTORY_SEPARATOR . 'build-config.php';

        if (!$buildDefinition instanceof Closure) {
            throw InvalidBuildDefinition::atPath($path);
        }

        $buildDefinitionBuilder = new BuildDefinitionBuilder();
        ($buildDefinition)($buildDefinitionBuilder);
        $this->definitions[$path] = $buildDefinitionBuilder->build();
    }

    private function get(string $path): BuildDefinition
    {
        $path = realpath($path);

        if (!isset($this->definitions[$path])) {
            $this->load($path);
        }

        return $this->definitions[$path];
    }

    public function target(TargetId $targetId): Target
    {
        return $this->get($targetId->path())->target($targetId->target());
    }

    /**
     * @return non-empty-list<string>
     */
    public function getActionNames(string $workingDirectory): array
    {
        return $this->get($workingDirectory)->targetNames();
    }
}