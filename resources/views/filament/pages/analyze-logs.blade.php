<x-filament-panels::page>
    {{-- Stepper --}}
    <div class="mb-6 flex items-center gap-2 text-sm">
        <div class="flex items-center gap-2">
            @if ($step === 1)
                <span class="flex h-7 w-7 items-center justify-center rounded-full text-xs font-semibold text-white" style="background-color: oklch(0.61 0.12 156.26)">1</span>
                <span class="font-medium" style="color: oklch(0.61 0.12 156.26)">Subir archivo de log</span>
            @else
                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-emerald-100 text-xs font-semibold text-emerald-700">
                    <x-heroicon-m-check class="h-4 w-4" />
                </span>
                <span class="text-gray-400">Subir archivo de log</span>
            @endif
        </div>

        <x-heroicon-o-chevron-right class="h-4 w-4 text-gray-300" />

        <div class="flex items-center gap-2">
            @if ($step === 2)
                <span class="flex h-7 w-7 items-center justify-center rounded-full text-xs font-semibold text-white" style="background-color: oklch(0.61 0.12 156.26)">2</span>
                <span class="font-medium" style="color: oklch(0.61 0.12 156.26)">Resultado del análisis</span>
            @else
                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-gray-100 text-xs font-semibold text-gray-400">2</span>
                <span class="text-gray-400">Resultado del análisis</span>
            @endif
        </div>
    </div>

    {{-- Paso 1: Subir archivo --}}
    @if ($step === 1)
        <form wire:submit="analyze">
            {{ $this->form }}

            <div class="mt-4 flex gap-2">
                <x-filament::button type="submit" :disabled="$isLoading" style="background-color: oklch(0.67 0.17 53.38)">
                    <span wire:loading.remove wire:target="analyze">Analizar archivo</span>
                    <span wire:loading wire:target="analyze" class="flex items-center gap-2">
                        <x-heroicon-o-arrow-path class="h-4 w-4 animate-spin" />
                        Analizando...
                    </span>
                </x-filament::button>
                @if ($fileName)
                    <x-filament::button wire:click="cancel" color="gray">
                        Cancelar
                    </x-filament::button>
                @endif
            </div>
        </form>

        @if ($isLoading)
            <div class="mt-4 flex items-center gap-2 text-sm text-gray-500">
                <x-heroicon-o-arrow-path class="h-4 w-4 animate-spin" />
                <span>Enviando archivo al servidor de análisis... esto puede tardar hasta 10 minutos.</span>
            </div>
        @endif

        @if ($statusMessage && !$analysis)
            <div class="mt-4 text-sm text-amber-600">
                {{ $statusMessage }}
            </div>
        @endif

        @if ($fileName)
            <div class="mt-6 space-y-4">
                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="px-5 py-3" style="background-color: oklch(0.61 0.12 156.26)">
                        <h4 class="text-sm font-semibold text-white">Archivo seleccionado</h4>
                    </div>
                    <div class="flex flex-wrap items-center gap-4 px-5 py-4 text-sm text-gray-700">
                        <div class="flex items-center gap-1.5">
                            <x-heroicon-o-document-text class="h-4 w-4" style="color: oklch(0.61 0.12 156.26)" />
                            <span class="font-medium">{{ $fileName }}</span>
                        </div>
                        @if ($fileSize)
                            <div class="flex items-center gap-1.5">
                                <x-heroicon-o-arrow-path class="h-4 w-4" style="color: oklch(0.61 0.12 156.26)" />
                                <span>{{ round($fileSize / 1024, 1) }} KB</span>
                            </div>
                        @endif
                    </div>
                </div>

                @if ($preview)
                    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                        <div class="px-5 py-3" style="background-color: oklch(0.61 0.12 156.26)">
                            <h4 class="text-sm font-semibold text-white">Vista previa (primeras 20 líneas)</h4>
                        </div>
                        <div class="px-5 py-4">
                            <pre class="overflow-x-auto rounded-lg bg-gray-900 p-4 text-xs text-emerald-400 leading-relaxed">{{ $preview }}</pre>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    @endif

    {{-- Paso 2: Resultados --}}
    @if ($step === 2 && $analysis)
        @php
            $aiAnalysis = $analysis['ai_analysis'] ?? null;
            $riskLevel = $aiAnalysis['risk_level'] ?? null;
            $lines = $analysis['lines'] ?? null;
            $riskClasses = [
                'bajo' => 'bg-emerald-100 text-emerald-700',
                'medio' => 'bg-amber-100 text-amber-700',
                'alto' => 'bg-orange-100 text-orange-700',
                'critico' => 'bg-rose-100 text-rose-700',
            ];
        @endphp

        <div class="space-y-4">
            <div class="flex gap-2">
                <x-filament::button wire:click="goBack">
                    Analizar otro archivo
                </x-filament::button>
            </div>

            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="px-5 py-3" style="background-color: oklch(0.61 0.12 156.26)">
                    <h4 class="text-sm font-semibold text-white">Archivo analizado</h4>
                </div>
                <div class="flex flex-wrap items-center gap-4 px-5 py-4 text-sm text-gray-700">
                    @if ($fileName)
                        <div class="flex items-center gap-1.5">
                            <x-heroicon-o-document-text class="h-4 w-4" style="color: oklch(0.61 0.12 156.26)" />
                            <span class="font-medium">{{ $fileName }}</span>
                        </div>
                    @endif
                    @if ($fileSize)
                        <div class="flex items-center gap-1.5">
                            <x-heroicon-o-arrow-path class="h-4 w-4" style="color: oklch(0.61 0.12 156.26)" />
                            <span>{{ round($fileSize / 1024, 1) }} KB</span>
                        </div>
                    @endif
                    @if ($lines)
                        <div class="flex items-center gap-1.5">
                            <x-heroicon-o-bars-3 class="h-4 w-4" style="color: oklch(0.61 0.12 156.26)" />
                            <span>{{ number_format($lines) }} líneas</span>
                        </div>
                    @endif
                </div>
            </div>

            @if ($aiAnalysis)
                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="px-5 py-3" style="background-color: oklch(0.61 0.12 156.26)">
                        <h4 class="text-sm font-semibold text-white">Resumen</h4>
                    </div>
                    <div class="px-5 py-4">
                        <p class="text-sm leading-6 text-gray-800">{{ $aiAnalysis['summary'] ?? 'No hay resumen disponible.' }}</p>
                    </div>
                </div>

                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="px-5 py-3" style="background-color: oklch(0.61 0.12 156.26)">
                        <h4 class="text-sm font-semibold text-white">Nivel de riesgo</h4>
                    </div>
                    <div class="px-5 py-4">
                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-wide {{ $riskClasses[$riskLevel] ?? 'bg-gray-100 text-gray-700' }}">
                            {{ $riskLevel ? ucfirst($riskLevel) : 'Desconocido' }}
                        </span>
                    </div>
                </div>

                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="px-5 py-3" style="background-color: oklch(0.61 0.12 156.26)">
                        <h4 class="text-sm font-semibold text-white">Hallazgos</h4>
                    </div>
                    <div class="px-5 py-4">
                        @if (!empty($aiAnalysis['findings']))
                            <ul class="space-y-2">
                                @foreach ($aiAnalysis['findings'] as $finding)
                                    <li class="rounded-lg border border-gray-200/60 bg-gray-50 px-4 py-2.5 text-sm text-gray-800">
                                        {{ $finding }}
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-sm text-gray-500">No se encontraron hallazgos.</p>
                        @endif
                    </div>
                </div>

                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="px-5 py-3" style="background-color: oklch(0.67 0.17 53.38)">
                        <h4 class="text-sm font-semibold text-white">Recomendaciones</h4>
                    </div>
                    <div class="px-5 py-4">
                        @if (!empty($aiAnalysis['recommendations']))
                            <ul class="space-y-2">
                                @foreach ($aiAnalysis['recommendations'] as $recommendation)
                                    <li class="rounded-lg border border-gray-200/60 bg-gray-50 px-4 py-2.5 text-sm text-gray-800">
                                        {{ $recommendation }}
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-sm text-gray-500">No se encontraron recomendaciones.</p>
                        @endif
                    </div>
                </div>
            @else
                <div class="rounded-xl bg-gray-900 p-4 text-sm text-white">
                    <p class="font-semibold">No se encontró información de análisis AI.</p>
                    <pre class="mt-3 overflow-x-auto text-xs">{{ json_encode($analysis, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            @endif
        </div>
    @endif
</x-filament-panels::page>
