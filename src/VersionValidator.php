<?php

declare (strict_types=1);
namespace Symplify\MonorepoBuilder;

use MonorepoBuilder202210\Symplify\ComposerJsonManipulator\FileSystem\JsonFileManager;
use MonorepoBuilder202210\Symplify\ComposerJsonManipulator\ValueObject\ComposerJson;
use MonorepoBuilder202210\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection;
use Symplify\MonorepoBuilder\Merge\Configuration\ModifyingComposerJsonProvider;
use Symplify\MonorepoBuilder\ValueObject\File;
use Symplify\MonorepoBuilder\ValueObject\Option;
use MonorepoBuilder202210\Symplify\PackageBuilder\Parameter\ParameterProvider;
use MonorepoBuilder202210\Symplify\SmartFileSystem\SmartFileInfo;
/**
 * @see \Symplify\MonorepoBuilder\Tests\VersionValidator\VersionValidatorTest
 */
final class VersionValidator
{
    /**
     * @var string[]
     */
    private const SECTIONS = [ComposerJsonSection::REQUIRE, ComposerJsonSection::REQUIRE_DEV];
    /**
     * @var \Symplify\ComposerJsonManipulator\FileSystem\JsonFileManager
     */
    private $jsonFileManager;
    /**
     * @var \Symplify\MonorepoBuilder\Merge\Configuration\ModifyingComposerJsonProvider
     */
    private $modifyingComposerJsonProvider;
    /**
     * @var \Symplify\PackageBuilder\Parameter\ParameterProvider
     */
    private $parameterProvider;
    public function __construct(JsonFileManager $jsonFileManager, ModifyingComposerJsonProvider $modifyingComposerJsonProvider, ParameterProvider $parameterProvider)
    {
        $this->jsonFileManager = $jsonFileManager;
        $this->modifyingComposerJsonProvider = $modifyingComposerJsonProvider;
        $this->parameterProvider = $parameterProvider;
    }
    /**
     * @param SmartFileInfo[] $smartFileInfos
     * @return string[][]
     */
    public function findConflictingPackageVersionsInFileInfos(array $smartFileInfos) : array
    {
        $packageVersionsPerFile = [];
        $packageVersionsPerFile = $this->appendAppendingComposerJson($packageVersionsPerFile);
        foreach ($smartFileInfos as $smartFileInfo) {
            $json = $this->jsonFileManager->loadFromFileInfo($smartFileInfo);
            foreach (self::SECTIONS as $section) {
                if (!isset($json[$section])) {
                    continue;
                }
                foreach ($json[$section] as $packageName => $packageVersion) {
                    $filePath = $smartFileInfo->getRelativeFilePathFromCwd();
                    $packageVersionsPerFile[$packageName][$filePath] = $packageVersion;
                }
            }
        }
        return $this->filterConflictingPackageVersionsPerFile($packageVersionsPerFile);
    }
    /**
     * @param mixed[] $packageVersionsPerFile
     * @return mixed[]
     */
    private function appendAppendingComposerJson(array $packageVersionsPerFile) : array
    {
        $appendingComposerJson = $this->modifyingComposerJsonProvider->getAppendingComposerJson();
        if (!$appendingComposerJson instanceof ComposerJson) {
            return $packageVersionsPerFile;
        }
        $requires = $appendingComposerJson->getRequire();
        foreach ($requires as $packageName => $packageVersion) {
            $packageVersionsPerFile[$packageName][File::CONFIG] = $packageVersion;
        }
        $requiredevs = $appendingComposerJson->getRequireDev();
        foreach ($requiredevs as $packageName => $packageVersion) {
            $packageVersionsPerFile[$packageName][File::CONFIG] = $packageVersion;
        }
        return $packageVersionsPerFile;
    }
    /**
     * @param array<string, array<string, string>> $packageVersionsPerFile
     * @return array<string, array<string, string>>
     */
    private function filterConflictingPackageVersionsPerFile(array $packageVersionsPerFile) : array
    {
        $conflictingPackageVersionsPerFile = [];
        foreach ($packageVersionsPerFile as $packageName => $filesToVersions) {
            $uniqueVersions = \array_unique($filesToVersions);
            if (\count($uniqueVersions) <= 1) {
                continue;
            }
            if ($this->isPackageAllowedVersionConflict($packageName)) {
                continue;
            }
            // sort by versions to make more readable
            \asort($filesToVersions);
            $conflictingPackageVersionsPerFile[$packageName] = $filesToVersions;
        }
        return $conflictingPackageVersionsPerFile;
    }
    private function isPackageAllowedVersionConflict(string $packageName) : bool
    {
        $excludePackageVersionConflicts = $this->parameterProvider->provideArrayParameter(Option::EXCLUDE_PACKAGE_VERSION_CONFLICTS);
        return \in_array($packageName, $excludePackageVersionConflicts, \true);
    }
}
