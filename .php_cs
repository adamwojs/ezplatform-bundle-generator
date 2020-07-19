<?php

return EzSystems\EzPlatformCodeStyle\PhpCsFixer\Config::create()->setFinder(
    PhpCsFixer\Finder::create()
        ->in(__DIR__ . '/src')
        ->in(__DIR__ . '/skeleton/3rd-party/src')
        ->in(__DIR__ . '/skeleton/3rd-party/tests')
        ->in(__DIR__ . '/skeleton/ibexa/src')
        ->in(__DIR__ . '/skeleton/ibexa/tests')
        ->in(__DIR__ . '/tests')
        ->files()->name('*.php')
);

