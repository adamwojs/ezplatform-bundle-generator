<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Platform\BundleGenerator\Generator;

final class BundleGeneratorConfiguration
{
    /** @var string|null */
    private $skeletonName;

    /** @var string|null */
    private $packageName;

    /** @var string|null */
    private $vendorName;

    /** @var string|null */
    private $vendorNamespace;

    /** @var string|null */
    private $bundleName;

    /** @var string|null */
    private $targetDir;

    public function getSkeletonName(): ?string
    {
        return $this->skeletonName;
    }

    public function setSkeletonName(?string $skeletonName): void
    {
        $this->skeletonName = $skeletonName;
    }

    public function getPackageName(): ?string
    {
        return $this->packageName;
    }

    public function setPackageName(?string $packageName): void
    {
        $this->packageName = $packageName;
    }

    public function getVendorName(): ?string
    {
        return $this->vendorName;
    }

    public function setVendorName(?string $vendorName): void
    {
        $this->vendorName = $vendorName;
    }

    public function getVendorNamespace(): ?string
    {
        return $this->vendorNamespace;
    }

    public function setVendorNamespace(?string $vendorNamespace): void
    {
        $this->vendorNamespace = $vendorNamespace;
    }

    public function getBundleName(): ?string
    {
        return $this->bundleName;
    }

    public function setBundleName(?string $bundleName): void
    {
        $this->bundleName = $bundleName;
    }

    public function getTargetDir(): ?string
    {
        return $this->targetDir;
    }

    public function setTargetDir(?string $targetDir): void
    {
        $this->targetDir = $targetDir;
    }
}
