<?php

namespace App\Filament\Resources\LogFiles;

use App\Filament\Resources\LogFiles\Pages\CreateLogFile;
use App\Filament\Resources\LogFiles\Pages\EditLogFile;
use App\Filament\Resources\LogFiles\Pages\ListLogFiles;
use App\Filament\Resources\LogFiles\Pages\ViewLogFile;
use App\Filament\Resources\LogFiles\Schemas\LogFileForm;
use App\Filament\Resources\LogFiles\Tables\LogFilesTable;
use App\Models\LogFile;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LogFileResource extends Resource
{
    protected static ?string $model = LogFile::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return LogFileForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LogFilesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLogFiles::route('/'),
            'create' => CreateLogFile::route('/create'),
            'view' => ViewLogFile::route('/{record}'),
            'edit' => EditLogFile::route('/{record}/edit'),
        ];
    }
}
