<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ResourceResource\Pages;
use App\Models\Resource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource as FilamentResource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use App\Enums\ResourceType;
use App\Enums\ResourceStatus;

class ResourceResource extends FilamentResource
{
    protected static ?string $model = Resource::class;

    protected static ?string $navigationIcon = 'heroicon-o-cloud';

    protected static ?string $navigationLabel = 'AWS Resources';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Resource Name')
                    ->placeholder('My S3 Bucket')
                    ->maxLength(255),

                Forms\Components\Select::make('type')
                    ->options([
                        ResourceType::S3_BUCKET->value => 'S3 Bucket',
                    ])
                    ->required()
                    ->disabled(fn (?Model $record) => $record !== null),

                Forms\Components\TextInput::make('configuration.bucket_name')
                    ->label('Bucket Name')
                    ->required()
                    ->maxLength(63)
                    ->helperText('Must be globally unique, lowercase, and contain only letters, numbers, dots, and hyphens')
                    ->disabled(fn (?Model $record) => $record !== null),

                Forms\Components\Select::make('configuration.region')
                    ->label('AWS Region')
                    ->options([
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
                    ])
                    ->required()
                    ->disabled(fn (?Model $record) => $record !== null),

                Forms\Components\Toggle::make('configuration.versioning_enabled')
                    ->label('Enable Versioning')
                    ->default(false)
                    ->disabled(fn (?Model $record) => $record !== null),

                Forms\Components\KeyValue::make('configuration.tags')
                    ->label('Tags')
                    ->keyLabel('Key')
                    ->valueLabel('Value')
                    ->reorderable(false)
                    ->disabled(fn (?Model $record) => $record !== null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge(),
                Tables\Columns\TextColumn::make('configuration.bucket_name')
                    ->label('Bucket Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('configuration.region')
                    ->label('Region'),
                Tables\Columns\IconColumn::make('configuration.versioning_enabled')
                    ->label('Versioning')
                    ->boolean(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state?->value ?? $state) {
                        ResourceStatus::PENDING->value => 'gray',
                        ResourceStatus::PROVISIONING->value => 'gray',
                        ResourceStatus::ACTIVE->value => 'success',
                        ResourceStatus::FAILED->value => 'danger',
                        ResourceStatus::DEPROVISIONING->value => 'warning',
                        ResourceStatus::DEPROVISIONED->value => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        ResourceType::S3_BUCKET->value => 'S3 Bucket',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        ResourceStatus::PENDING->value => 'Pending',
                        ResourceStatus::PROVISIONING->value => 'Provisioning',
                        ResourceStatus::ACTIVE->value => 'Active',
                        ResourceStatus::FAILED->value => 'Failed',
                        ResourceStatus::DEPROVISIONING->value => 'Deprovisioning',
                        ResourceStatus::DEPROVISIONED->value => 'Deprovisioned',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('deprovision')
                    ->action(fn (Resource $record) => $record->deprovision())
                    ->requiresConfirmation()
                    ->color('danger')
                    ->icon('heroicon-o-trash')
                    ->visible(fn (Resource $record) => $record->status === ResourceStatus::ACTIVE),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('deprovision')
                    ->action(fn (Collection $records) => $records->each->deprovision())
                    ->requiresConfirmation()
                    ->color('danger')
                    ->icon('heroicon-o-trash')
                    ->deselectRecordsAfterCompletion(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListResources::route('/'),
            'create' => Pages\CreateResource::route('/create'),
            'view' => Pages\ViewResource::route('/{record}'),
        ];
    }
}
