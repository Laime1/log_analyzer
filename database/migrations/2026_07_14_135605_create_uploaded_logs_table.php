<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('uploaded_logs', function (Blueprint $table) {
            $table->id();
             $table->string('name'); // Nombre original del archivo
            $table->string('file_path'); // Ruta donde se guarda físicamente
            $table->longText('content')->nullable(); // Contenido del archivo (para mostrarlo)
            $table->string('status')->default('pending'); // pending, processed, error
            $table->json('analysis_result')->nullable(); // Resultado del análisis (de FastAPI)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uploaded_logs');
    }
};
