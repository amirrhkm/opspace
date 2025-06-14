namespace App\Filament\Resources\ResourceResource\Pages;

use App\Filament\Resources\ResourceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateResource extends CreateRecord
{
    protected static string $resource = ResourceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = 'pending';
        $data['configuration'] = [
            'bucket_name' => $data['bucket_name'] ?? null,
            'region' => $data['region'] ?? 'us-east-1',
            'versioning_enabled' => $data['versioning_enabled'] ?? false,
            'environment' => $data['environment'] ?? 'development',
        ];

        // Remove form-only fields
        unset($data['bucket_name']);
        unset($data['versioning_enabled']);
        unset($data['environment']);

        return $data;
    }
} 