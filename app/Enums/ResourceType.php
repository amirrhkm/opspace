<?php

namespace App\Enums;

enum ResourceType: string
{
    case S3_BUCKET = 'S3_BUCKET';
    // We can add more resource types here in the future
    
    public function getLabel(): string
    {
        return match($this) {
            self::S3_BUCKET => 'S3 Bucket',
        };
    }

    public function getTerraformModulePath(): string
    {
        return match($this) {
            self::S3_BUCKET => 'aws-s3-bucket',
        };
    }
} 