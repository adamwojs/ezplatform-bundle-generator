<?php

declare(strict_types=1);

namespace AdamWojs\EzPlatformBundleGenerator\Generator;

use Symfony\Component\Filesystem\Filesystem;

final class BundleGenerator
{
    private const FILE_TEMPLATES = [
        '/src/bundle/DependencyInjection/__BUNDLE_NAME__Extension.php',
        '/src/bundle/__BUNDLE_NAME__Bundle.php',
        '/phpunit.xml.dist',
        '/composer.json',
        '/README.md',
    ];

    private const FILE_NAME_TEMPLATES = [
        '/src/bundle/DependencyInjection/__BUNDLE_NAME__Extension.php',
        '/src/bundle/__BUNDLE_NAME__Bundle.php',
    ];

    public function generate(BundleGeneratorConfiguration $config): void
    {
        $fs = new Filesystem();
        $fs->mirror(__DIR__ . '/../../skeleton', $config->getTargetDir());

        $replacements = [
            '__PACKAGE_NAME__' => $config->getPackageName(),
            '__VENDOR_NAME__' => $config->getVendorName(),
            '__VENDOR_NAMESPACE__' => $config->getVendorNamespace(),
            '__BUNDLE_NAME__' => $config->getBundleName(),
        ];

        foreach (self::FILE_TEMPLATES as $filename) {
            $content = strtr(file_get_contents($config->getTargetDir() . $filename), $replacements);

            $fs->dumpFile($config->getTargetDir() . $filename, $content);
        }

        foreach (self::FILE_NAME_TEMPLATES as $filename) {
            $fs->rename(
                $config->getTargetDir() . $filename,
                $config->getTargetDir() . strtr($filename, $replacements)
            );
        }
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
            return implode('', array_map('ucfirst', explode('-', $packageName)));
        }

        return null;
    }
}
