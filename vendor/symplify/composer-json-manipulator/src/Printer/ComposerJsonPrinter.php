<?php

declare (strict_types=1);
namespace MonorepoBuilder202210\Symplify\ComposerJsonManipulator\Printer;

use MonorepoBuilder202210\Symplify\ComposerJsonManipulator\FileSystem\JsonFileManager;
use MonorepoBuilder202210\Symplify\ComposerJsonManipulator\ValueObject\ComposerJson;
use MonorepoBuilder202210\Symplify\SmartFileSystem\SmartFileInfo;
/**
 * @api
 */
final class ComposerJsonPrinter
{
    /**
     * @var \Symplify\ComposerJsonManipulator\FileSystem\JsonFileManager
     */
    private $jsonFileManager;
    public function __construct(JsonFileManager $jsonFileManager)
    {
        $this->jsonFileManager = $jsonFileManager;
    }
    public function printToString(ComposerJson $composerJson) : string
    {
        return $this->jsonFileManager->encodeJsonToFileContent($composerJson->getJsonArray());
    }
    /**
     * @param string|\Symplify\SmartFileSystem\SmartFileInfo $targetFile
     */
    public function print(ComposerJson $composerJson, $targetFile) : void
    {
        if (\is_string($targetFile)) {
            $this->jsonFileManager->printComposerJsonToFilePath($composerJson, $targetFile);
            return;
        }
        $this->jsonFileManager->printJsonToFileInfo($composerJson->getJsonArray(), $targetFile);
    }
}
