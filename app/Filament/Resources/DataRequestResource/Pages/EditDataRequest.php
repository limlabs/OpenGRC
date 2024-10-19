<?php

namespace App\Filament\Resources\DataRequestResource\Pages;

use App\Filament\Resources\DataRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDataRequest extends EditRecord
{
    protected static string $resource = DataRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
