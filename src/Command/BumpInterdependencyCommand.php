<?php

declare (strict_types=1);
namespace Symplify\MonorepoBuilder\Command;

use MonorepoBuilder202210\Symfony\Component\Console\Input\InputArgument;
use MonorepoBuilder202210\Symfony\Component\Console\Input\InputInterface;
use MonorepoBuilder202210\Symfony\Component\Console\Output\OutputInterface;
use Symplify\MonorepoBuilder\DependencyUpdater;
use Symplify\MonorepoBuilder\FileSystem\ComposerJsonProvider;
use Symplify\MonorepoBuilder\Package\PackageNamesProvider;
use Symplify\MonorepoBuilder\Validator\SourcesPresenceValidator;
use MonorepoBuilder202210\Symplify\PackageBuilder\Console\Command\AbstractSymplifyCommand;
use MonorepoBuilder202210\Symplify\SymplifyKernel\Exception\ShouldNotHappenException;
final class BumpInterdependencyCommand extends AbstractSymplifyCommand
{
    /**
     * @var string
     */
    private const VERSION_ARGUMENT = 'version';
    /**
     * @var \Symplify\MonorepoBuilder\DependencyUpdater
     */
    private $dependencyUpdater;
    /**
     * @var \Symplify\MonorepoBuilder\FileSystem\ComposerJsonProvider
     */
    private $composerJsonProvider;
    /**
     * @var \Symplify\MonorepoBuilder\Validator\SourcesPresenceValidator
     */
    private $sourcesPresenceValidator;

    /**
     * @var PackageNamesProvider
     */
    private $packageNamesProvider;

    public function __construct(DependencyUpdater $dependencyUpdater, ComposerJsonProvider $composerJsonProvider, SourcesPresenceValidator $sourcesPresenceValidator, PackageNamesProvider $packageNamesProvider)
    {
        $this->dependencyUpdater = $dependencyUpdater;
        $this->composerJsonProvider = $composerJsonProvider;
        $this->sourcesPresenceValidator = $sourcesPresenceValidator;
        parent::__construct();
        $this->packageNamesProvider = $packageNamesProvider;
    }
    protected function configure() : void
    {
        $this->setName('bump-interdependency');
        $this->setDescription('Bump dependency of split packages on each other');
        $this->addArgument(self::VERSION_ARGUMENT, InputArgument::REQUIRED, 'New version of inter-dependencies, e.g. "^4.4.2"');
    }
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        /** @var string $version */
        $version = $input->getArgument(self::VERSION_ARGUMENT);
        $this->dependencyUpdater->updateFileInfosWithPackagesAndVersion($this->composerJsonProvider->getPackagesComposerFileInfos(), $this->packageNamesProvider->provide(), $version);
        $successMessage = \sprintf('Inter-dependencies of packages were updated to "%s".', $version);
        $this->symfonyStyle->success($successMessage);
        return self::SUCCESS;
    }
}
