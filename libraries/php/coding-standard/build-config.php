<?php

declare(strict_types=1);

use Ramona\AutomationPlatformLibBuild\Actions\NoOp;
use Ramona\AutomationPlatformLibBuild\Definition\BuildDefinitionBuilder;
use Ramona\AutomationPlatformLibBuild\Targets\Target;

return static function (BuildDefinitionBuilder $builder) {
    $builder->addTarget('build', new NoOp());
    $builder->addTarget('fix', new NoOp());
};
