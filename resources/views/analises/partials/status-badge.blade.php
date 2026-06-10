@php
    $cores = [
        \App\Models\Analise::STATUS_RASCUNHO => 'bg-gray-100 text-gray-700',
        \App\Models\Analise::STATUS_EM_ANDAMENTO => 'bg-blue-100 text-blue-700',
        \App\Models\Analise::STATUS_CONCLUIDA => 'bg-green-100 text-green-700',
    ];
@endphp
<span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $cores[$analise->status] ?? 'bg-gray-100 text-gray-700' }}">
    {{ $analise->statusLabel() }}
</span>
