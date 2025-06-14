namespace App\Enums;

enum ResourceStatus: string
{
    case PENDING = 'pending';
    case PROVISIONING = 'provisioning';
    case ACTIVE = 'active';
    case FAILED = 'failed';
    case DEPROVISIONING = 'deprovisioning';
    case DEPROVISIONED = 'deprovisioned';

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