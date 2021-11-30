<?php

declare(strict_types=1);

namespace Ramona\AutomationPlatformLibBuild\Actions;

use Ramona\AutomationPlatformLibBuild\BuildOutput\TargetOutput;
use Ramona\AutomationPlatformLibBuild\BuildResult;
use Ramona\AutomationPlatformLibBuild\Context;

/**
 * @api
 */
final class Group implements BuildAction
{
    /**
     * @param non-empty-list<BuildAction> $actions
     */
    public function __construct(private array $actions)
    {
    }

    public function execute(TargetOutput $output, Context $context, string $workingDirectory): BuildResult
    {
        $artifacts = [];
        foreach ($this->actions as $action) {
            $result = $action->execute($output, $context, $workingDirectory);

            if (!$result->hasSucceeded()) {
                return $result;
            }

            foreach ($result->artifacts() as $artifact) {
                $artifacts[] = $artifact;
            }
        }

        return BuildResult::ok($artifacts);
    }
}
