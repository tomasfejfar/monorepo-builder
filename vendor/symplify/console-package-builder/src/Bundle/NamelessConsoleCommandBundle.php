<?php

declare (strict_types=1);
namespace MonorepoBuilder20211020\Symplify\ConsolePackageBuilder\Bundle;

use MonorepoBuilder20211020\Symfony\Component\DependencyInjection\ContainerBuilder;
use MonorepoBuilder20211020\Symfony\Component\HttpKernel\Bundle\Bundle;
use MonorepoBuilder20211020\Symplify\ConsolePackageBuilder\DependencyInjection\CompilerPass\NamelessConsoleCommandCompilerPass;
final class NamelessConsoleCommandBundle extends \MonorepoBuilder20211020\Symfony\Component\HttpKernel\Bundle\Bundle
{
    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder
     */
    public function build($containerBuilder) : void
    {
        $containerBuilder->addCompilerPass(new \MonorepoBuilder20211020\Symplify\ConsolePackageBuilder\DependencyInjection\CompilerPass\NamelessConsoleCommandCompilerPass());
    }
}
