<?php

namespace App\Filament\Resources\LogFiles\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class LogFileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                FileUpload::make('file_path')
                    ->label('Archivo de log')
                    ->hiddenLabel()
                    ->directory('log-files')
                    ->acceptedFileTypes([
                        'text/plain',
                        'text/*',
                        'application/octet-stream',
                    ])
                    ->rule('mimes:log,txt')
                    ->maxSize(10240)
                    ->required()
                    ->visible(fn (string $operation): bool => $operation !== 'view')
                    ->afterStateUpdated(function (Set $set, mixed $state): void {
                        if (blank($state)) {
                            $set('name', null);
                            $set('type', null);
                            $set('size', null);
                            $set('content', null);

                            return;
                        }

                        if ($state instanceof TemporaryUploadedFile) {
                            $set('name', $state->getClientOriginalName());
                            $set('type', $state->getMimeType());
                            $set('size', $state->getSize());
                            $set('content', $state->get());

                            return;
                        }

                        if (is_string($state) && Storage::disk('local')->exists($state)) {
                            $set('name', basename($state));
                            $set('type', Storage::disk('local')->mimeType($state));
                            $set('size', Storage::disk('local')->size($state));
                            $set('content', Storage::disk('local')->get($state));
                        }
                    }),

                TextInput::make('name')
                    ->label('Nombre del archivo')
                    ->disabled()
                    ->visible(fn (Get $get): bool => filled($get('file_path'))),

                TextInput::make('type')
                    ->label('Tipo de archivo')
                    ->disabled()
                    ->visible(fn (Get $get): bool => filled($get('file_path'))),

                TextInput::make('size')
                    ->label('Tamaño (bytes)')
                    ->disabled()
                    ->visible(fn (Get $get): bool => filled($get('file_path'))),

                Textarea::make('content')
                    ->label('Contenido')
                    ->rows(20)
                    ->disabled()
                    ->visible(fn (Get $get): bool => filled($get('file_path'))),
            ]);
    }
}
