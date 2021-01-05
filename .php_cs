<?php

return EzSystems\EzPlatformCodeStyle\PhpCsFixer\EzPlatformInternalConfigFactory::build()->setFinder(
    PhpCsFixer\Finder::create()
        ->in(__DIR__ . '/src')
        ->in(__DIR__ . '/skeleton/3rd-party/src')
        ->in(__DIR__ . '/skeleton/3rd-party/tests')
        ->in(__DIR__ . '/skeleton/ibexa-ee/src')
        ->in(__DIR__ . '/skeleton/ibexa-ee/tests')
        ->in(__DIR__ . '/skeleton/ibexa-oss/src')
        ->in(__DIR__ . '/skeleton/ibexa-oss/tests')
        ->in(__DIR__ . '/tests')
        ->files()->name('*.php')
);

