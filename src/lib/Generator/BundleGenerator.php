<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Platform\BundleGenerator\Generator;

use FilesystemIterator;
use Iterator;
use RecursiveCallbackFilterIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Symfony\Component\Filesystem\Filesystem;

final class BundleGenerator
{
    public const DEFAULT_SKELETON_NAME = 'ibexa-ee';

    private const SKELETON_DIRECTORY = __DIR__ . '/../../../skeleton/';

    public function generate(BundleGeneratorConfiguration $config): void
    {
        $fs = new Filesystem();
        $fs->mirror($this->getSkeletonDirectory($config), $config->getTargetDir());

        $replacements = [
            '__PACKAGE_NAME__' => $config->getPackageName(),
            '__VENDOR_NAME__' => $config->getVendorName(),
            '__VENDOR_NAMESPACE__' => $config->getVendorNamespace(),
            '__BUNDLE_NAME__' => $config->getBundleName(),
        ];

        $iterator = $this->createSkeletonIterator($config->getTargetDir());

        foreach ($iterator as $file) {
            if (!$file->isDir()) {
                $content = strtr(file_get_contents($file->getPathname()), $replacements);
                $fs->dumpFile($file->getPathname(), $content);
            }
        }

        foreach ($iterator as $file) {
            $fs->rename($file->getPathname(), strtr($file->getPathname(), $replacements), true);
        }
    }

    private function createSkeletonIterator(string $targetDir): Iterator
    {
        $excludedPaths = [
            $targetDir . '/vendor',
            $targetDir . '/composer.lock',
        ];

        $flags = FilesystemIterator::CURRENT_AS_FILEINFO | FilesystemIterator::SKIP_DOTS;

        $iterator = new RecursiveCallbackFilterIterator(
            new RecursiveDirectoryIterator($targetDir, $flags),
            static function (SplFileInfo $file) use ($excludedPaths): bool {
                return !in_array($file->getPathname(), $excludedPaths);
            }
        );

        return new RecursiveIteratorIterator($iterator);
    }

    private function getSkeletonDirectory(BundleGeneratorConfiguration $config): string
    {
        $skeletonName = $config->getSkeletonName();
        if (empty($skeletonName)) {
            $skeletonName = self::DEFAULT_SKELETON_NAME;
        }

        return self::SKELETON_DIRECTORY . $skeletonName . '/';
    }

    public static function getDefaultPackageName(): string
    {
        return basename(realpath('.'));
    }

    public static function getDefaultVendorName(): ?string
    {
        return ($name = getenv('VENDOR_NAME')) ? $name : null;
    }

    public static function getDefaultVendorNamespace(?string $packageVendor): ?string
    {
        if (($namespace = getenv('VENDOR_NAMESPACE')) !== false) {
            return $namespace;
        }

        if ($packageVendor !== null) {
            return ucfirst($packageVendor);
        }

        return null;
    }

    public static function getDefaultBundleName(?string $packageName): ?string
    {
        if ($packageName !== null) {
            return implode('', array_map(static function (string $chunk) {
                if (strtolower($chunk) === 'ezplatform') {
                    return 'EzPlatform';
                }

                return ucfirst($chunk);
            }, explode('-', $packageName)));
        }

        return null;
    }

    public static function getAvailableSkeletons(): array
    {
        $skeletons = [];

        $iterator = new FilesystemIterator(self::SKELETON_DIRECTORY);
        foreach ($iterator as $fileInfo) {
            if (!$fileInfo->isDir()) {
                continue;
            }

            $skeletons[] = $fileInfo->getBasename();
        }

        return $skeletons;
    }
}
