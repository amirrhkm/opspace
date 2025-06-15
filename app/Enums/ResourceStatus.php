<?php

namespace App\Enums;

enum ResourceStatus: string
{
    case PENDING = 'PENDING';
    case PROVISIONING = 'PROVISIONING';
    case ACTIVE = 'ACTIVE';
    case FAILED = 'FAILED';
    case DEPROVISIONING = 'DEPROVISIONING';
    case DEPROVISIONED = 'DEPROVISIONED';

    public function getColor(): string
    {
        return match($this) {
            self::PENDING => 'gray',
            self::PROVISIONING => 'orange',
            self::ACTIVE => 'green',
            self::FAILED => 'red',
            self::DEPROVISIONING => 'purple',
            self::DEPROVISIONED => 'blue',
        };
    }
} 