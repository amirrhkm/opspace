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
} 