<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class AnalyzeLogs extends Page
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.analyze-logs';
    protected static string $routePath = '/analyze-logs';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Analizar logs';

    public array $data = [];
    public ?string $content = null;
    public ?array $analysis = null;
    public ?string $statusMessage = null;
    public bool $isLoading = false;

    protected function getFormStatePath(): ?string
    {
        return 'data';
    }

    protected function getFormSchema(): array
    {
        return [
            FileUpload::make('file')
                ->label('Archivo de log')
                ->acceptedFileTypes([
                    '.log',
                    'text/plain',
                    'text/*',
                    'application/octet-stream',
                ])
                ->required()
                ->storeFiles(false) // no lo persistimos, solo lo leemos/enviamos
                ->maxSize(10240),
        ];
    }

    public function loadFile(): void
    {
        $formState = $this->form->getState();
        $file = $formState['file'] ?? null;

        if ($file instanceof TemporaryUploadedFile) {
            $this->content = $file->get();

            return;
        }

        if (is_string($file) && Storage::disk('local')->exists($file)) {
            $this->content = Storage::disk('local')->get($file);
        }
    }

    public function analyze(): void
    {
        $this->analysis = null;
        $this->statusMessage = null;
        $this->isLoading = true;

        try {
            $formState = $this->form->getState();
            $file = $formState['file'] ?? null;

            if (! $file instanceof TemporaryUploadedFile) {
                $this->statusMessage = 'No se encontró un archivo válido para enviar.';
                return;
            }

            $response = Http::timeout(60)
                ->attach(
                    'file',
                    $file->get(),
                    $file->getClientOriginalName()
                )
                ->post(config('services.log_analyzer.url') . '/analyze');

            if ($response->successful()) {
                $this->analysis = $response->json();
                $this->statusMessage = 'Análisis completado correctamente.';
            } else {
                $this->statusMessage = 'Error del servidor de análisis: ' . $response->status();
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            $this->statusMessage = 'No se pudo conectar con el servicio de análisis (FastAPI). ¿Está corriendo?';
        } catch (\Throwable $e) {
            $this->statusMessage = 'Ocurrió un error inesperado: ' . $e->getMessage();
        } finally {
            $this->isLoading = false;
        }
    }
}