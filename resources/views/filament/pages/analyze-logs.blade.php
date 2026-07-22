<x-filament-panels::page>
    <form wire:submit="analyze">
        {{ $this->form }}

        <div class="mt-4 flex gap-2">
            <x-filament::button type="submit" :disabled="$isLoading">
                {{ $isLoading ? 'Analizando...' : 'Enviar a análisis' }}
            </x-filament::button>
        </div>
    </form>

    @if ($statusMessage)
        <div class="mt-4 text-sm text-amber-600">
            {{ $statusMessage }}
        </div>
    @endif

    @if ($analysis)
        @php
            $aiAnalysis = $analysis['ai_analysis'] ?? null;
            $riskLevel = $aiAnalysis['risk_level'] ?? null;
            $filename = $analysis['filename'] ?? null;
            $size = $analysis['size'] ?? null;
            $lines = $analysis['lines'] ?? null;
            $riskClasses = [
                'bajo' => 'bg-emerald-100 text-emerald-700',
                'medio' => 'bg-amber-100 text-amber-700',
                'alto' => 'bg-orange-100 text-orange-700',
                'critico' => 'bg-rose-100 text-rose-700',
            ];
        @endphp

        <div class="mt-6 space-y-4">
            <h3 class="text-lg font-semibold tracking-tight" style="color: oklch(0.61 0.12 156.26)">Resultado del análisis</h3>

            @if ($aiAnalysis)
                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="px-5 py-3" style="background-color: oklch(0.61 0.12 156.26)">
                        <h4 class="text-sm font-semibold text-white">Archivo analizado</h4>
                    </div>
                    <div class="flex flex-wrap items-center gap-4 px-5 py-4 text-sm text-gray-700">
                        @if ($filename)
                            <div class="flex items-center gap-1.5">
                                <x-heroicon-o-document-text class="h-4 w-4" style="color: oklch(0.61 0.12 156.26)" />
                                <span class="font-medium">{{ $filename }}</span>
                            </div>
                        @endif
                        @if ($size)
                            <div class="flex items-center gap-1.5">
                                <x-heroicon-o-arrow-path class="h-4 w-4" style="color: oklch(0.61 0.12 156.26)" />
                                <span>{{ round($size / 1024, 1) }} KB</span>
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

                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="bg-gray-200 px-5 py-3">
                        <h4 class="text-sm font-semibold text-gray-700">JSON de respuesta (debug)</h4>
                    </div>
                    <div class="px-5 py-4">
                        <pre class="overflow-x-auto text-xs text-emerald-600 leading-relaxed">{{ json_encode($analysis, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
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
