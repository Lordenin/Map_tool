<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('clientes.show', $analise->cliente) }}" class="text-sm text-gray-500 hover:text-gray-700">
                    {{ $analise->cliente->nome }}
                </a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $analise->titulo }}
                </h2>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('analises.edit', $analise) }}"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                    {{ __('Editar') }}
                </a>
                <a href="{{ route('clientes.show', $analise->cliente) }}" class="text-sm text-gray-600 hover:text-gray-900">
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
                        <dt class="text-sm font-medium text-gray-500">{{ __('Status') }}</dt>
                        <dd class="text-sm text-gray-900 sm:col-span-2">
                            @include('analises.partials.status-badge')
                        </dd>
                    </div>
                    <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-500">{{ __('Data da análise') }}</dt>
                        <dd class="text-sm text-gray-900 sm:col-span-2">{{ $analise->data_analise?->format('d/m/Y') ?? '—' }}</dd>
                    </div>
                    <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-500">{{ __('Pontos') }}</dt>
                        <dd class="text-sm text-gray-900 sm:col-span-2">{{ $analise->pontos_count }}</dd>
                    </div>
                    <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-500">{{ __('Descrição') }}</dt>
                        <dd class="text-sm text-gray-900 sm:col-span-2 whitespace-pre-line">{{ $analise->descricao ?: '—' }}</dd>
                    </div>
                </dl>

                <div class="mt-6 border-t border-gray-100 pt-6">
                    <form method="POST" action="{{ route('analises.destroy', $analise) }}"
                        onsubmit="return confirm('Remover a análise {{ $analise->titulo }}? Esta ação não pode ser desfeita.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm text-red-600 hover:text-red-900">
                            {{ __('Remover análise') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
