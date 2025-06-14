<?php

namespace App\Contracts;

use App\Models\Resource;

interface CloudProviderInterface
{
    public function validateConfiguration(array $configuration): bool;
    public function getResourceConfiguration(Resource $resource): array;
    public function getResourceOutputs(Resource $resource): array;
    public function getRegions(): array;
    public function validateBucketName(string $name): bool;
} 