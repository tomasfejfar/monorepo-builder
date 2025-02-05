<?php

declare (strict_types=1);
namespace MonorepoBuilder202210\Symplify\SymplifyKernel;

use MonorepoBuilder202210\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use MonorepoBuilder202210\Symfony\Component\DependencyInjection\ContainerBuilder;
use MonorepoBuilder202210\Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use MonorepoBuilder202210\Symplify\SymplifyKernel\Contract\Config\LoaderFactoryInterface;
use MonorepoBuilder202210\Symplify\SymplifyKernel\DependencyInjection\LoadExtensionConfigsCompilerPass;
use MonorepoBuilder202210\Webmozart\Assert\Assert;
/**
 * @see \Symplify\SymplifyKernel\Tests\ContainerBuilderFactory\ContainerBuilderFactoryTest
 */
final class ContainerBuilderFactory
{
    /**
     * @var \Symplify\SymplifyKernel\Contract\Config\LoaderFactoryInterface
     */
    private $loaderFactory;
    public function __construct(LoaderFactoryInterface $loaderFactory)
    {
        $this->loaderFactory = $loaderFactory;
    }
    /**
     * @param string[] $configFiles
     * @param CompilerPassInterface[] $compilerPasses
     * @param ExtensionInterface[] $extensions
     */
    public function create(array $configFiles, array $compilerPasses, array $extensions) : ContainerBuilder
    {
        Assert::allIsAOf($extensions, ExtensionInterface::class);
        Assert::allIsAOf($compilerPasses, CompilerPassInterface::class);
        Assert::allString($configFiles);
        Assert::allFile($configFiles);
        $containerBuilder = new ContainerBuilder();
        $this->registerExtensions($containerBuilder, $extensions);
        $this->registerConfigFiles($containerBuilder, $configFiles);
        $this->registerCompilerPasses($containerBuilder, $compilerPasses);
        // this calls load() method in every extensions
        // ensure these extensions are implicitly loaded
        $compilerPassConfig = $containerBuilder->getCompilerPassConfig();
        $compilerPassConfig->setMergePass(new LoadExtensionConfigsCompilerPass());
        return $containerBuilder;
    }
    /**
     * @param ExtensionInterface[] $extensions
     */
    private function registerExtensions(ContainerBuilder $containerBuilder, array $extensions) : void
    {
        foreach ($extensions as $extension) {
            $containerBuilder->registerExtension($extension);
        }
    }
    /**
     * @param CompilerPassInterface[] $compilerPasses
     */
    private function registerCompilerPasses(ContainerBuilder $containerBuilder, array $compilerPasses) : void
    {
        foreach ($compilerPasses as $compilerPass) {
            $containerBuilder->addCompilerPass($compilerPass);
        }
    }
    /**
     * @param string[] $configFiles
     */
    private function registerConfigFiles(ContainerBuilder $containerBuilder, array $configFiles) : void
    {
        $delegatingLoader = $this->loaderFactory->create($containerBuilder, \getcwd());
        foreach ($configFiles as $configFile) {
            $delegatingLoader->load($configFile);
        }
    }
}
