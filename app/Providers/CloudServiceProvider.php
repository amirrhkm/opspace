namespace App\Providers;

use App\Contracts\CloudProviderInterface;
use App\Services\AWS\AWSCloudProvider;
use Illuminate\Support\ServiceProvider;

class CloudServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CloudProviderInterface::class, AWSCloudProvider::class);
    }

    public function boot(): void
    {
        //
    }
} 