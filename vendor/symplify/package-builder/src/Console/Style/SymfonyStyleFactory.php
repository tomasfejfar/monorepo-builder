<?php

declare (strict_types=1);
namespace MonorepoBuilder202210\Symplify\PackageBuilder\Console\Style;

use MonorepoBuilder202210\Symfony\Component\Console\Application;
use MonorepoBuilder202210\Symfony\Component\Console\Input\ArgvInput;
use MonorepoBuilder202210\Symfony\Component\Console\Output\ConsoleOutput;
use MonorepoBuilder202210\Symfony\Component\Console\Output\OutputInterface;
use MonorepoBuilder202210\Symfony\Component\Console\Style\SymfonyStyle;
use MonorepoBuilder202210\Symplify\PackageBuilder\Reflection\PrivatesCaller;
/**
 * @api
 */
final class SymfonyStyleFactory
{
    /**
     * @var \Symplify\PackageBuilder\Reflection\PrivatesCaller
     */
    private $privatesCaller;
    public function __construct()
    {
        $this->privatesCaller = new PrivatesCaller();
    }
    public function create() : SymfonyStyle
    {
        // to prevent missing argv indexes
        if (!isset($_SERVER['argv'])) {
            $_SERVER['argv'] = [];
        }
        $argvInput = new ArgvInput();
        $consoleOutput = new ConsoleOutput();
        // to configure all -v, -vv, -vvv options without memory-lock to Application run() arguments
        $this->privatesCaller->callPrivateMethod(new Application(), 'configureIO', [$argvInput, $consoleOutput]);
        // --debug is called
        if ($argvInput->hasParameterOption('--debug')) {
            $consoleOutput->setVerbosity(OutputInterface::VERBOSITY_DEBUG);
        }
        // disable output for tests
        if ($this->isPHPUnitRun()) {
            $consoleOutput->setVerbosity(OutputInterface::VERBOSITY_QUIET);
        }
        return new SymfonyStyle($argvInput, $consoleOutput);
    }
    /**
     * Never ever used static methods if not neccesary, this is just handy for tests + src to prevent duplication.
     */
    private function isPHPUnitRun() : bool
    {
        return \defined('MonorepoBuilder202210\\PHPUNIT_COMPOSER_INSTALL') || \defined('MonorepoBuilder202210\\__PHPUNIT_PHAR__');
    }
}
