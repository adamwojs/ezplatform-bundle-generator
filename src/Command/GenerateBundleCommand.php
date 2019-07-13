<?php

declare(strict_types=1);

namespace AdamWojs\EzPlatformBundleGenerator\Command;

use AdamWojs\EzPlatformBundleGenerator\Generator\BundleGenerator;
use AdamWojs\EzPlatformBundleGenerator\Generator\BundleGeneratorConfiguration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

final class GenerateBundleCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('generate-bundle');

        $this->addArgument(
            'package-name',
            InputArgument::OPTIONAL,
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
            $defaultPackageName = BundleGenerator::getDefaultPackageName();

            $question = new Question(
                "Package name e.g ezplatform-page-builder [$defaultPackageName]: ",
                $defaultPackageName
            );

            $input->setArgument('package-name', $helper->ask($input, $output, $question));
        }

        if (!$input->getOption('vendor-name')) {
            $defaultVendorName = BundleGenerator::getDefaultVendorName();

            $question = new Question(
                'Package vendor name e.g ezsystems [' . ($defaultVendorName ?? 'n/a') . ']: ',
                $defaultVendorName
            );

            $input->setOption('vendor-name', $helper->ask($input, $output, $question));
        }

        if (!$input->getOption('vendor-namespace')) {
            $defaultVendorNamespace = BundleGenerator::getDefaultVendorNamespace(
                $input->getOption('vendor-name')
            );

            $question = new Question(
                'Bundle vendor namespace e.g EzSystems [' . ($defaultVendorNamespace ?? 'n/a') . ']: ',
                $defaultVendorNamespace
            );

            $input->setOption('vendor-namespace', $helper->ask($input, $output, $question));
        }

        if (!$input->getOption('bundle-name')) {
            $defaultBundleName = BundleGenerator::getDefaultBundleName(
                $input->getArgument('package-name')
            );

            $question = new Question(
                "Bundle name without 'Bundle' suffix e.g EzPlatformPageBuilder [" . ($defaultBundleName ?? 'n/a') . ']: ',
                $defaultBundleName
            );

            $input->setOption('bundle-name', $helper->ask($input, $output, $question));
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $config = new BundleGeneratorConfiguration();
        $config->setTargetDir($input->getArgument('target-dir'));
        $config->setPackageName($input->getArgument('package-name'));
        $config->setVendorName($input->getOption('vendor-name'));
        $config->setVendorNamespace($input->getOption('vendor-namespace'));
        $config->setBundleName($input->getOption('bundle-name'));

        $generator = new BundleGenerator();
        $generator->generate($config);
    }
}
