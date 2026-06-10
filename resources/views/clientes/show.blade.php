<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $cliente->nome }}
            </h2>
            <div class="flex items-center gap-3">
                <a href="{{ route('clientes.edit', $cliente) }}"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                    {{ __('Editar') }}
                </a>
                <a href="{{ route('clientes.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                    {{ __('Voltar') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 rounded-md bg-green-50 p-4 text-sm text-green-700 border border-green-200">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <dl class="divide-y divide-gray-100">
                    <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-500">{{ __('Segmento') }}</dt>
                        <dd class="text-sm text-gray-900 sm:col-span-2">{{ $cliente->segmento ?: '—' }}</dd>
                    </div>
                    <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-500">{{ __('Observações') }}</dt>
                        <dd class="text-sm text-gray-900 sm:col-span-2 whitespace-pre-line">{{ $cliente->observacoes ?: '—' }}</dd>
                    </div>
                    <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-500">{{ __('Cadastrado por') }}</dt>
                        <dd class="text-sm text-gray-900 sm:col-span-2">{{ $cliente->user?->name ?? '—' }}</dd>
                    </div>
                </dl>

                <div class="mt-6 border-t border-gray-100 pt-6">
                    <form method="POST" action="{{ route('clientes.destroy', $cliente) }}"
                        onsubmit="return confirm('Remover o cliente {{ $cliente->nome }}? Esta ação não pode ser desfeita.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm text-red-600 hover:text-red-900">
                            {{ __('Remover cliente') }}
                        </button>
                    </form>
                </div>
            </div>

            <div class="mt-6 bg-white shadow-sm sm:rounded-lg">
                <div class="flex items-center justify-between p-6 border-b border-gray-100">
                    <h3 class="font-semibold text-lg text-gray-800">
                        {{ __('Análises') }}
                        <span class="text-sm font-normal text-gray-400">({{ $cliente->analises->count() }})</span>
                    </h3>
                    <a href="{{ route('clientes.analises.create', $cliente) }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition">
                        {{ __('Nova análise') }}
                    </a>
                </div>

                @if ($cliente->analises->isEmpty())
                    <div class="p-6 text-center text-gray-500">
                        {{ __('Nenhuma análise para este cliente ainda.') }}
                    </div>
                @else
                    <ul class="divide-y divide-gray-100">
                        @foreach ($cliente->analises as $analise)
                            <li class="flex items-center justify-between gap-4 px-6 py-4 hover:bg-gray-50">
                                <div class="min-w-0">
                                    <a href="{{ route('analises.show', $analise) }}" class="font-medium text-gray-900 hover:text-indigo-600">
                                        {{ $analise->titulo }}
                                    </a>
                                    <div class="mt-1 flex items-center gap-3 text-xs text-gray-500">
                                        @include('analises.partials.status-badge')
                                        <span>{{ $analise->data_analise?->format('d/m/Y') ?? 'sem data' }}</span>
                                        <span>{{ $analise->pontos_count }} {{ \Illuminate\Support\Str::plural('ponto', $analise->pontos_count) }}</span>
                                    </div>
                                </div>
                                <a href="{{ route('analises.edit', $analise) }}" class="text-sm text-indigo-600 hover:text-indigo-900 whitespace-nowrap">
                                    {{ __('Editar') }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
