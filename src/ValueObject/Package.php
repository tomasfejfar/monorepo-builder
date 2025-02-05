<?php

declare (strict_types=1);
namespace Symplify\MonorepoBuilder\ValueObject;

use MonorepoBuilder202210\Nette\Utils\Strings;
final class Package
{
    /**
     * @var string
     */
    private $shortName;
    /**
     * @var bool
     */
    private $hasTests;
    public function __construct(string $name, bool $hasTests)
    {
        $this->hasTests = $hasTests;
        $this->shortName = (string) Strings::after($name, '/', -1);
    }
    public function getShortName() : string
    {
        return $this->shortName;
    }
    public function hasTests() : bool
    {
        return $this->hasTests;
    }
}
