<?php

namespace App\Filament\Resources\LogFiles\Pages;

use App\Filament\Resources\LogFiles\LogFileResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLogFile extends EditRecord
{
    protected static string $resource = LogFileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
