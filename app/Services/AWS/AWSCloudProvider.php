namespace App\Services\AWS;

use App\Contracts\CloudProviderInterface;
use App\Models\Resource;
use App\Enums\ResourceType;

class AWSCloudProvider implements CloudProviderInterface
{
    public function validateConfiguration(array $configuration): bool
    {
        // To Do: validation based on resource type
        return true;
    }

    public function getResourceConfiguration(Resource $resource): array
    {
        return match($resource->type) {
            ResourceType::S3_BUCKET => [
                'bucket_name' => $resource->configuration['bucket_name'],
                'region' => $resource->configuration['region'] ?? 'us-east-1',
                'versioning_enabled' => $resource->configuration['versioning_enabled'] ?? false,
                'tags' => [
                    'ManagedBy' => 'OpSpace',
                    'Environment' => $resource->configuration['environment'] ?? 'development',
                ],
            ],
            default => throw new \InvalidArgumentException("Unsupported resource type: {$resource->type->value}"),
        };
    }

    public function getResourceOutputs(Resource $resource): array
    {
        return $resource->terraform_state['outputs'] ?? [];
    }

    public function getRegions(): array
    {
        return [
            'us-east-1' => 'US East (N. Virginia)',
            'us-east-2' => 'US East (Ohio)',
            'us-west-1' => 'US West (N. California)',
            'us-west-2' => 'US West (Oregon)',
            'eu-west-1' => 'EU (Ireland)',
            'eu-central-1' => 'EU (Frankfurt)',
            'ap-southeast-1' => 'Asia Pacific (Singapore)',
            'ap-southeast-2' => 'Asia Pacific (Sydney)',
            'ap-northeast-1' => 'Asia Pacific (Tokyo)',
            'ap-northeast-2' => 'Asia Pacific (Seoul)',
            'ap-northeast-3' => 'Asia Pacific (Osaka)',
            'ca-central-1' => 'Canada (Central)',
            'cn-north-1' => 'China (Beijing)',
        ];
    }

    public function validateBucketName(string $name): bool
    {
        // AWS S3 bucket naming rules
        $pattern = '/^[a-z0-9][a-z0-9.-]*[a-z0-9]$/';
        $length = strlen($name);

        return $length >= 3 && 
               $length <= 63 && 
               preg_match($pattern, $name) && 
               !str_contains($name, '..') &&
               !filter_var($name, FILTER_VALIDATE_IP) &&
               !str_starts_with($name, 'xn--');
    }
} 