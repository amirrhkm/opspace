namespace App\Filament\Resources;

use App\Filament\Resources\ResourceResource\Pages;
use App\Models\Resource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource as FilamentResource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Enums\ResourceType;
use App\Enums\ResourceStatus;

class ResourceResource extends FilamentResource
{
    protected static ?string $model = Resource::class;

    protected static ?string $navigationIcon = 'heroicon-o-cloud';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('type')
                    ->options(collect(ResourceType::cases())->mapWithKeys(fn ($type) => [$type->value => $type->getLabel()]))
                    ->required(),
                Forms\Components\TextInput::make('bucket_name')
                    ->required()
                    ->visible(fn (Forms\Get $get) => $get('type') === ResourceType::S3_BUCKET->value)
                    ->maxLength(255),
                Forms\Components\Select::make('region')
                    ->options([
                        'us-east-1' => 'US East (N. Virginia)',
                        'us-west-2' => 'US West (Oregon)',
                        'eu-west-1' => 'EU (Ireland)',
                    ])
                    ->default('us-east-1')
                    ->required(),
                Forms\Components\Toggle::make('versioning_enabled')
                    ->visible(fn (Forms\Get $get) => $get('type') === ResourceType::S3_BUCKET->value)
                    ->default(false),
                Forms\Components\Select::make('environment')
                    ->options([
                        'development' => 'Development',
                        'staging' => 'Staging',
                        'production' => 'Production',
                    ])
                    ->default('development')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->formatStateUsing(fn (ResourceType $state) => $state->getLabel()),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (ResourceStatus $state) => $state->getColor()),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('provision')
                    ->action(function (Resource $record) {
                        app(\App\Services\Terraform\TerraformService::class)->provision($record);
                    })
                    ->requiresConfirmation()
                    ->visible(fn (Resource $record) => 
                        in_array($record->status, [ResourceStatus::PENDING, ResourceStatus::FAILED])),
                Tables\Actions\Action::make('deprovision')
                    ->action(function (Resource $record) {
                        app(\App\Services\Terraform\TerraformService::class)->deprovision($record);
                    })
                    ->requiresConfirmation()
                    ->color('danger')
                    ->visible(fn (Resource $record) => $record->status === ResourceStatus::ACTIVE),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListResources::route('/'),
            'create' => Pages\CreateResource::route('/create'),
            'edit' => Pages\EditResource::route('/{record}/edit'),
        ];
    }
} 