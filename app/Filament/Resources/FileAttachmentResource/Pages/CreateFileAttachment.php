<?php

namespace App\Filament\Resources\FileAttachmentResource\Pages;

use App\Filament\Resources\FileAttachmentResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateFileAttachment extends CreateRecord
{
    protected static string $resource = FileAttachmentResource::class;
}
