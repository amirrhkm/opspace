namespace App\Models;

use App\Enums\ResourceStatus;
use App\Enums\ResourceType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Resource extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'status',
        'configuration',
        'terraform_state',
        'last_error',
    ];

    protected $casts = [
        'type' => ResourceType::class,
        'status' => ResourceStatus::class,
        'configuration' => 'array',
        'terraform_state' => 'array',
    ];

    public function markAsProvisioning(): void
    {
        $this->update(['status' => ResourceStatus::PROVISIONING]);
    }

    public function markAsActive(): void
    {
        $this->update(['status' => ResourceStatus::ACTIVE]);
    }

    public function markAsFailed(string $error): void
    {
        $this->update([
            'status' => ResourceStatus::FAILED,
            'last_error' => $error,
        ]);
    }

    public function markAsDeprovisioning(): void
    {
        $this->update(['status' => ResourceStatus::DEPROVISIONING]);
    }

    public function markAsDeprovisioned(): void
    {
        $this->update(['status' => ResourceStatus::DEPROVISIONED]);
    }
} 