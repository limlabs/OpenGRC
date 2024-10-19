<?php

namespace App\Filament\Resources\AuditItemResource\Pages;

use App\Filament\Resources\AuditItemResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAuditItem extends CreateRecord
{
    protected static string $resource = AuditItemResource::class;
}
