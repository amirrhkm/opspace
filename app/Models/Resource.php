<?php

namespace App\Models;

use App\Enums\ResourceStatus;
use App\Enums\ResourceType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Services\Terraform\TerraformService;

class Resource extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'status',
        'configuration',
        'terraform_state',
        'error_message',
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

    public function markAsFailed(string $message): void
    {
        $this->update([
            'status' => ResourceStatus::FAILED,
            'error_message' => $message,
        ]);
    }

    public function markAsDeprovisioning(): void
    {
        $this->update(['status' => ResourceStatus::DEPROVISIONING]);
    }

    public function markAsDeprovisioned(): void
    {
        $this->update([
            'status' => ResourceStatus::DEPROVISIONED,
            'terraform_state' => null,
        ]);
    }

    public function deprovision(): void
    {
        $this->update(['status' => ResourceStatus::DEPROVISIONING]);
        
        try {
            app(TerraformService::class)->deprovision($this);
        } catch (\Exception $e) {
            $this->markAsFailed($e->getMessage());
            throw $e;
        }
    }
} 