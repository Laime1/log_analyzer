<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class LogFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'file_path',
        'type',
        'size',
        'content',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'size' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (LogFile $logFile): void {
            if (! $logFile->isDirty('file_path') || blank($logFile->file_path)) {
                return;
            }

            $logFile->populateFromUploadedFile();
        });
    }

    protected function populateFromUploadedFile(): void
    {
        $disk = Storage::disk('local');

        if (! $disk->exists($this->file_path)) {
            return;
        }

        $this->name = basename($this->file_path);
        $this->type = $disk->mimeType($this->file_path);
        $this->size = $disk->size($this->file_path);
        $this->content = $disk->get($this->file_path);
        $this->metadata = [
            'uploaded_at' => now()->toDateTimeString(),
        ];
    }
}