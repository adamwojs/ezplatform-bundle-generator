<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Platform\BundleGenerator\Composer;

use Composer\IO\IOInterface;
use Composer\Script\Event;
use Ibexa\Platform\BundleGenerator\Generator\BundleGenerator;
use Ibexa\Platform\BundleGenerator\Generator\BundleGeneratorConfiguration;

final class ScriptHandler
{
    public static function generate(Event $event): void
    {
        $io = $event->getIO();

        $config = new BundleGeneratorConfiguration();
        $config->setSkeletonName(self::askForSkeletonName($io));
        $config->setPackageName(self::askForPackageName($io, $config));
        $config->setVendorName(self::askForVendorName($io, $config));
        $config->setVendorNamespace(self::askForVendorNamespace($io, $config));
        $config->setBundleName(self::askForBundleName($io, $config));
        $config->setTargetDir(realpath('.'));

        $generator = new BundleGenerator();
        $generator->generate($config);
    }

    private static function askForPackageName(IOInterface $io, BundleGeneratorConfiguration $config): ?string
    {
        $defaultPackageName = BundleGenerator::getDefaultPackageName();

        return $io->ask(
            "Package name e.g ibexa-page-builder [$defaultPackageName]: ",
            $defaultPackageName
        );
    }

    private static function askForVendorName(IOInterface $io, BundleGeneratorConfiguration $config): ?string
    {
        $defaultVendorName = BundleGenerator::getDefaultVendorName();

        return $io->ask(
            'Package vendor name e.g ibexa [' . ($defaultVendorName ?? 'n/a') . ']: ',
            $defaultVendorName
        );
    }

    private static function askForVendorNamespace(IOInterface $io, BundleGeneratorConfiguration $config): ?string
    {
        $defaultVendorNamespace = BundleGenerator::getDefaultVendorNamespace($config->getVendorName());

        return $io->ask(
            'Bundle vendor namespace e.g Ibexa [' . ($defaultVendorNamespace ?? 'n/a') . ']: ',
            $defaultVendorNamespace
        );
    }

    private static function askForBundleName(IOInterface $io, BundleGeneratorConfiguration $config): ?string
    {
        $defaultBundleName = BundleGenerator::getDefaultBundleName($config->getPackageName());

        return $io->ask(
            "Bundle name without 'Bundle' suffix e.g IbexaPageBuilder [" . ($defaultBundleName ?? 'n/a') . ']: ',
            $defaultBundleName
        );
    }

    private static function askForSkeletonName(IOInterface $io): ?string
    {
        $skeletons = BundleGenerator::getAvailableSkeletons();
        if (count($skeletons) === 1) {
            return reset($skeletons);
        }

        return $io->select(
            'Skeleton',
            array_combine($skeletons, $skeletons),
            BundleGenerator::DEFAULT_SKELETON_NAME
        );
    }
}
