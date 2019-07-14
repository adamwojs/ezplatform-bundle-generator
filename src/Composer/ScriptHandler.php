<?php

declare(strict_types=1);

namespace AdamWojs\EzPlatformBundleGenerator\Composer;

use AdamWojs\EzPlatformBundleGenerator\Generator\BundleGenerator;
use AdamWojs\EzPlatformBundleGenerator\Generator\BundleGeneratorConfiguration;
use Composer\IO\IOInterface;
use Composer\Script\Event;

final class ScriptHandler
{
    public static function generate(Event $event): void
    {
        $io = $event->getIO();

        $config = new BundleGeneratorConfiguration();
        $config->setPackageName(self::askForPackageName($io, $config));
        $config->setVendorName(self::askForVendorName($io, $config));
        $config->setVendorNamespace(self::askForVendorNamespace($io, $config));
        $config->setBundleName(self::askForBundleName($io, $config));
        $config->setTargetDir(realpath('.'));

        $generator = new BundleGenerator();
        $generator->generate($config);
    }

    private static function askForPackageName(IOInterface $io, BundleGeneratorConfiguration $config): string
    {
        $defaultPackageName = BundleGenerator::getDefaultPackageName();

        return $io->ask(
            "Package name e.g ezplatform-page-builder [$defaultPackageName]: ",
            $defaultPackageName
        );
    }

    private static function askForVendorName(IOInterface $io, BundleGeneratorConfiguration $config): string
    {
        $defaultVendorName = BundleGenerator::getDefaultVendorName();

        return $io->ask(
            'Package vendor name e.g ezsystems [' . ($defaultVendorName ?? 'n/a') . ']: ',
            $defaultVendorName
        );
    }

    private static function askForVendorNamespace(IOInterface $io, BundleGeneratorConfiguration $config): string
    {
        $defaultVendorNamespace = BundleGenerator::getDefaultVendorNamespace($config->getVendorName());

        return $io->ask(
            'Bundle vendor namespace e.g EzSystems [' . ($defaultVendorNamespace ?? 'n/a') . ']: ',
            $defaultVendorNamespace
        );
    }

    private static function askForBundleName(IOInterface $io, BundleGeneratorConfiguration $config): string
    {
        $defaultBundleName = BundleGenerator::getDefaultBundleName($config->getPackageName());

        return $io->ask(
            "Bundle name without 'Bundle' suffix e.g EzPlatformPageBuilder [" . ($defaultBundleName ?? 'n/a') . ']: ',
            $defaultBundleName
        );
    }
}
