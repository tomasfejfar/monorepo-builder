<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace MonorepoBuilder20211020\Symfony\Component\Console\Exception;

/**
 * Represents an incorrect command name typed in the console.
 *
 * @author Jérôme Tamarelle <jerome@tamarelle.net>
 */
class CommandNotFoundException extends \InvalidArgumentException implements \MonorepoBuilder20211020\Symfony\Component\Console\Exception\ExceptionInterface
{
    private $alternatives;
    /**
     * @param string          $message      Exception message to throw
     * @param string[]        $alternatives List of similar defined names
     * @param int             $code         Exception code
     * @param \Throwable|null $previous     Previous exception used for the exception chaining
     */
    public function __construct(string $message, array $alternatives = [], int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->alternatives = $alternatives;
    }
    /**
     * @return string[] A list of similar defined names
     */
    public function getAlternatives()
    {
        return $this->alternatives;
    }
}
