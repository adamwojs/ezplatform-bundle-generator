<?php

require __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Filesystem\Filesystem;

final class GenerateCommand extends Command
{
    private const FILE_TEMPLATES = [
        '/src/bundle/DependencyInjection/__BUNDLE_NAME__Extension.php',
        '/src/bundle/__BUNDLE_NAME__Bundle.php',
        '/phpunit.xml.dist',
        '/composer.json',
        '/README.md'
    ];

    private const FILE_NAME_TEMPLATES = [
        '/src/bundle/DependencyInjection/__BUNDLE_NAME__Extension.php',
        '/src/bundle/__BUNDLE_NAME__Bundle.php',
    ];

    protected function configure(): void
    {
        $this->setName('generate');

        $this->addArgument(
            'package-name',
            InputArgument::REQUIRED,
            'Package name e.g ezplatform-page-builder'
        );

        $this->addArgument(
            'target-dir',
            InputArgument::OPTIONAL,
            'Target directory',
            'target'
        );

        $this->addOption(
            'vendor-name',
            null,
            InputOption::VALUE_REQUIRED,
            'Package vendor name e.g. ezsystems'
        );

        $this->addOption(
            'vendor-namespace',
            null,
            InputOption::VALUE_REQUIRED,
            'e.g. EzSystems'
        );

        $this->addOption(
            'bundle-name',
            null,
            InputOption::VALUE_REQUIRED,
            'e.g. EzPlatformPageBuilder'
        );
    }

    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        $helper = $this->getHelper('question');

        if (!$input->getArgument('package-name')) {
            $question = new Question(
                "Package name e.g ezplatform-page-builder [{$this->getDefaultPackageName()}]: ",
                $this->getDefaultPackageName()
            );

            $input->setArgument('package-name', $helper->ask($input, $output, $question));
        }

        if (!$input->getOption('vendor-name')) {
            $defaultVendorName = $this->getDefaultPackageVendor();

            $question = new Question(
                "Package vendor name e.g ezsystems [" . ($defaultVendorName ?? 'n/a') . "]: ",
                $defaultVendorName
            );

            $input->setOption('vendor-name', $helper->ask($input, $output, $question));
        }

        if (!$input->getOption('vendor-namespace')) {
            $defaultVendorNamespace = $this->getDefaultVendorNamespace($input->getOption('vendor-name'));

            $question = new Question(
                "Bundle vendor namespace e.g EzSystems [" . ($defaultVendorNamespace ?? 'n/a') . "]: ",
                $defaultVendorNamespace
            );

            $input->setOption('vendor-namespace', $helper->ask($input, $output, $question));
        }

        if (!$input->getOption('bundle-name')) {
            $defaultBundleName = $this->getDefaultBundleName($input->getArgument('package-name'));

            $question = new Question(
                "Bundle name without 'Bundle' suffix e.g EzPlatformPageBuilder [" . ($defaultBundleName ?? 'n/a') . "]: ",
                $defaultBundleName
            );

            $input->setOption('bundle-name', $helper->ask($input, $output, $question));
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $targetDirectory = $input->getArgument('target-dir');

        $fs = new Filesystem();
        $fs->mirror('skeleton', $targetDirectory);

        $replacements = [
            '__PACKAGE_NAME__' => $input->getArgument('package-name'),
            '__VENDOR_NAME__' => $input->getOption('vendor-name'),
            '__VENDOR_NAMESPACE__' => $input->getOption('vendor-namespace'),
            '__BUNDLE_NAME__' => $input->getOption('bundle-name'),
        ];

        foreach (self::FILE_TEMPLATES as $filename) {
            $content = strtr(file_get_contents($targetDirectory . $filename), $replacements);

            $fs->dumpFile($targetDirectory . $filename, $content);
        }

        foreach (self::FILE_NAME_TEMPLATES as $filename) {
            $fs->rename($targetDirectory . $filename, $targetDirectory . strtr($filename, $replacements));
        }
    }

    private function getDefaultPackageName(): string
    {
        return basename(realpath("."));
    }

    private function getDefaultPackageVendor(): ?string
    {
        return ($name = getenv('VENDOR_NAME')) ? $name : null;
    }

    private function getDefaultVendorNamespace(?string $packageVendor): ?string
    {
        if (($namespace = getenv('VENDOR_NAMESPACE')) !== false) {
            return $namespace;
        }

        if ($packageVendor !== null) {
            return ucfirst($packageVendor);
        }

        return null;
    }

    private function getDefaultBundleName(?string $packageName): ?string
    {
        if ($packageName !== null) {
            return implode('', array_map('ucfirst', explode('-', $packageName)));
        }

        return null;
    }
}

$application = new Application();
$application->add(new GenerateCommand());
$application->setDefaultCommand('generate', true);
$application->run();
