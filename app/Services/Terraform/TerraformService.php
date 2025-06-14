namespace App\Services\Terraform;

use App\Models\Resource;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

class TerraformService
{
    private string $terraformDir;

    public function __construct()
    {
        $this->terraformDir = storage_path('app/terraform');
    }

    public function provision(Resource $resource): void
    {
        $resourceDir = $this->getResourceDirectory($resource);
        
        try {
            // Generate tfvars file
            $this->generateTfvars($resource, $resourceDir);
            
            $this->initializeTerraform($resourceDir);
            $this->applyTerraform($resourceDir);
            
            // Update resource state
            $state = json_decode(file_get_contents("{$resourceDir}/terraform.tfstate"), true);
            $resource->update([
                'terraform_state' => $state,
                'status' => \App\Enums\ResourceStatus::ACTIVE,
            ]);
        } catch (\Exception $e) {
            $resource->markAsFailed($e->getMessage());
            throw $e;
        }
    }

    public function deprovision(Resource $resource): void
    {
        $resourceDir = $this->getResourceDirectory($resource);
        
        try {
            // Ensure tfvars file exists
            if (!file_exists("{$resourceDir}/terraform.tfvars")) {
                $this->generateTfvars($resource, $resourceDir);
            }
            
            $this->initializeTerraform($resourceDir);
            $this->destroyTerraform($resourceDir);
            
            $resource->markAsDeprovisioned();
        } catch (\Exception $e) {
            $resource->markAsFailed($e->getMessage());
            throw $e;
        }
    }

    private function getResourceDirectory(Resource $resource): string
    {
        return match($resource->type) {
            \App\Enums\ResourceType::S3_BUCKET => "{$this->terraformDir}/s3",
            default => throw new \InvalidArgumentException("Unsupported resource type: {$resource->type->value}"),
        };
    }

    private function generateTfvars(Resource $resource, string $workingDir): void
    {
        $config = $resource->configuration;
        $tfvars = [];
        
        foreach ($config as $key => $value) {
            if (is_bool($value)) {
                $tfvars[] = "{$key} = " . ($value ? "true" : "false");
            } elseif (is_array($value)) {
                $tfvars[] = "{$key} = " . json_encode($value);
            } else {
                $tfvars[] = "{$key} = \"{$value}\"";
            }
        }
        
        file_put_contents("{$workingDir}/terraform.tfvars", implode("\n", $tfvars));
    }

    private function initializeTerraform(string $workingDir): void
    {
        $process = new Process(['terraform', 'init'], $workingDir);
        $process->setTimeout(300);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }
    }

    private function applyTerraform(string $workingDir): void
    {
        $process = new Process(['terraform', 'apply', '-auto-approve'], $workingDir);
        $process->setTimeout(300);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }
    }

    private function destroyTerraform(string $workingDir): void
    {
        $process = new Process(['terraform', 'destroy', '-auto-approve'], $workingDir);
        $process->setTimeout(300);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }
    }
} 