<?php

declare (strict_types=1);
namespace Symplify\MonorepoBuilder\Kernel;

use MonorepoBuilder202210\Psr\Container\ContainerInterface;
use MonorepoBuilder202210\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonManipulatorConfig;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;
use MonorepoBuilder202210\Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutowireInterfacesCompilerPass;
use MonorepoBuilder202210\Symplify\PackageBuilder\ValueObject\ConsoleColorDiffConfig;
use MonorepoBuilder202210\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
final class MonorepoBuilderKernel extends AbstractSymplifyKernel
{
    /**
     * @param string[] $configFiles
     */
    public function createFromConfigs(array $configFiles) : ContainerInterface
    {
        $configFiles[] = __DIR__ . '/../../config/config.php';
        $configFiles[] = ComposerJsonManipulatorConfig::FILE_PATH;
        $configFiles[] = ConsoleColorDiffConfig::FILE_PATH;
        $autowireInterfacesCompilerPass = new AutowireInterfacesCompilerPass([ReleaseWorkerInterface::class]);
        $compilerPasses = [$autowireInterfacesCompilerPass];
        return $this->create($configFiles, $compilerPasses, []);
    }
}
