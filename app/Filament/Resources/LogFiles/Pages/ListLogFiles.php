<?php

namespace App\Filament\Resources\LogFiles\Pages;

use App\Filament\Resources\LogFiles\LogFileResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLogFiles extends ListRecords
{
    protected static string $resource = LogFileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
