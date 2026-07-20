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
        <div class="mt-4 text-sm">
            {{ $statusMessage }}
        </div>
    @endif

    @if ($analysis)
        @php
            $aiAnalysis = $analysis['ai_analysis'] ?? null;
            $riskLevel = $aiAnalysis['risk_level'] ?? null;
            $riskClasses = [
                'bajo' => 'bg-emerald-100 text-emerald-700',
                'medio' => 'bg-amber-100 text-amber-700',
                'alto' => 'bg-orange-100 text-orange-700',
                'critico' => 'bg-rose-100 text-rose-700',
            ];
        @endphp

        <div class="mt-6 space-y-4">
            <h3 class="text-lg font-semibold tracking-tight">Resultado del análisis</h3>

            @if ($aiAnalysis)
                <div class="rounded-xl border border-slate-200/80 bg-white/80 p-5 shadow-sm">
                    <div class="space-y-2">
                        <h4 class="text-sm font-semibold text-slate-700">Resumen</h4>
                        <p class="text-sm leading-6 text-slate-800">{{ $aiAnalysis['summary'] ?? 'No hay resumen disponible.' }}</p>
                    </div>
                </div>

                <div class="rounded-xl border border-slate-200/80 bg-white/80 p-5 shadow-sm">
                    <div class="flex flex-wrap items-center gap-3">
                        <h4 class="text-sm font-semibold text-slate-700">Nivel de riesgo</h4>
                        <span class="rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-wide {{ $riskClasses[$riskLevel] ?? 'bg-slate-100 text-slate-700' }}">
                            {{ $riskLevel ? ucfirst($riskLevel) : 'Desconocido' }}
                        </span>
                    </div>
                </div>

                <div class="rounded-xl border border-slate-200/80 bg-white/80 p-5 shadow-sm">
                    <div class="space-y-2">
                        <h4 class="text-sm font-semibold text-slate-700">Hallazgos</h4>
                        @if (!empty($aiAnalysis['findings']))
                            <ul class="space-y-2">
                                @foreach ($aiAnalysis['findings'] as $finding)
                                    <li class="rounded-lg border border-slate-200/60 bg-slate-50/80 px-4 py-2.5 text-sm text-slate-800">
                                        {{ $finding }}
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-sm text-slate-500">No se encontraron hallazgos.</p>
                        @endif
                    </div>
                </div>

                <div class="rounded-xl border border-slate-200/80 bg-white/80 p-5 shadow-sm">
                    <div class="space-y-2">
                        <h4 class="text-sm font-semibold text-slate-700">Recomendaciones</h4>
                        @if (!empty($aiAnalysis['recommendations']))
                            <ul class="space-y-2">
                                @foreach ($aiAnalysis['recommendations'] as $recommendation)
                                    <li class="rounded-lg border border-emerald-200/60 bg-emerald-50/80 px-4 py-2.5 text-sm text-emerald-800">
                                        {{ $recommendation }}
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-sm text-slate-500">No se encontraron recomendaciones.</p>
                        @endif
                    </div>
                </div>
            @else
                <div class="rounded-xl border border-slate-200/80 bg-slate-950 p-4 text-sm text-white">
                    <p class="font-semibold">No se encontró información de análisis AI.</p>
                    <pre class="mt-3 overflow-x-auto text-xs">{{ json_encode($analysis, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            @endif
        </div>
    @endif
</x-filament-panels::page>