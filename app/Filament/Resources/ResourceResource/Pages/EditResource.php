namespace App\Filament\Resources\ResourceResource\Pages;

use App\Filament\Resources\ResourceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditResource extends EditRecord
{
    protected static string $resource = ResourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Extract configuration values for form fields
        return array_merge($data, [
            'bucket_name' => $data['configuration']['bucket_name'] ?? null,
            'region' => $data['configuration']['region'] ?? 'us-east-1',
            'versioning_enabled' => $data['configuration']['versioning_enabled'] ?? false,
            'environment' => $data['configuration']['environment'] ?? 'development',
        ]);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
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