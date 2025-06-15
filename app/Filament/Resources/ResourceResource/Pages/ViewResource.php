<?php

namespace App\Filament\Resources\ResourceResource\Pages;

use App\Filament\Resources\ResourceResource;
use Filament\Resources\Pages\ViewRecord;
use App\Enums\ResourceStatus;
use Filament\Actions\Action;

class ViewResource extends ViewRecord
{
    protected static string $resource = ResourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('deprovision')
                ->action(fn () => $this->record->deprovision())
                ->requiresConfirmation()
                ->color('danger')
                ->icon('heroicon-o-trash')
                ->visible(fn () => $this->record->status === ResourceStatus::ACTIVE),
        ];
    }
} 