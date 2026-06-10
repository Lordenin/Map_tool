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

            {{-- Estabelecimento: ponto de referência das distâncias --}}
            <div class="mt-6 bg-white shadow-sm sm:rounded-lg">
                <div class="flex items-center justify-between p-6 border-b border-gray-100">
                    <h3 class="font-semibold text-lg text-gray-800">{{ __('Estabelecimento') }}</h3>
                    @unless ($analise->estabelecimento)
                        <a href="{{ route('analises.pontos.create', ['analise' => $analise, 'tipo' => 'estabelecimento']) }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition">
                            {{ __('Definir estabelecimento') }}
                        </a>
                    @endunless
                </div>

                @if ($analise->estabelecimento)
                    @php $estab = $analise->estabelecimento; @endphp
                    <div class="p-6 flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="font-medium text-gray-900">{{ $estab->nome }}</p>
                            <p class="text-sm text-gray-500">{{ $estab->endereco ?: '—' }}</p>
                            <a href="https://www.google.com/maps?q={{ $estab->latitude }},{{ $estab->longitude }}"
                                target="_blank" rel="noopener" class="text-xs text-indigo-600 hover:underline">
                                {{ $estab->latitude }}, {{ $estab->longitude }} ↗
                            </a>
                            @if ($estab->observacoes)
                                <p class="mt-2 text-sm text-gray-600 whitespace-pre-line">{{ $estab->observacoes }}</p>
                            @endif
                        </div>
                        <div class="flex items-center gap-3 whitespace-nowrap">
                            <a href="{{ route('pontos.edit', $estab) }}" class="text-sm text-indigo-600 hover:text-indigo-900">{{ __('Editar') }}</a>
                            <form method="POST" action="{{ route('pontos.destroy', $estab) }}" class="inline"
                                onsubmit="return confirm('Remover o estabelecimento? As distâncias dos concorrentes serão zeradas.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm text-red-600 hover:text-red-900">{{ __('Remover') }}</button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="p-6 text-center text-gray-500">
                        {{ __('Defina o estabelecimento do cliente para calcular as distâncias dos concorrentes.') }}
                    </div>
                @endif
            </div>

            {{-- Concorrentes: distância pré-calculada (Haversine) até o estabelecimento --}}
            <div class="mt-6 bg-white shadow-sm sm:rounded-lg">
                <div class="flex items-center justify-between p-6 border-b border-gray-100">
                    <h3 class="font-semibold text-lg text-gray-800">
                        {{ __('Concorrentes') }}
                        <span class="text-sm font-normal text-gray-400">({{ $analise->concorrentes->count() }})</span>
                    </h3>
                    <a href="{{ route('analises.pontos.create', ['analise' => $analise, 'tipo' => 'concorrente']) }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition">
                        {{ __('Adicionar concorrente') }}
                    </a>
                </div>

                @if ($analise->concorrentes->isEmpty())
                    <div class="p-6 text-center text-gray-500">{{ __('Nenhum concorrente cadastrado ainda.') }}</div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">{{ __('Nome') }}</th>
                                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">{{ __('Endereço') }}</th>
                                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">{{ __('Distância') }}</th>
                                    <th class="px-6 py-3 text-right font-medium text-gray-500 uppercase tracking-wider">{{ __('Ações') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($analise->concorrentes as $concorrente)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 font-medium text-gray-900">
                                            <a href="https://www.google.com/maps?q={{ $concorrente->latitude }},{{ $concorrente->longitude }}"
                                                target="_blank" rel="noopener" class="hover:text-indigo-600">
                                                {{ $concorrente->nome }} ↗
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 text-gray-500">{{ $concorrente->endereco ?: '—' }}</td>
                                        <td class="px-6 py-4 text-gray-900">{{ $concorrente->distanciaFormatada() ?? '—' }}</td>
                                        <td class="px-6 py-4 text-right whitespace-nowrap">
                                            <a href="{{ route('pontos.edit', $concorrente) }}" class="text-indigo-600 hover:text-indigo-900">{{ __('Editar') }}</a>
                                            <form method="POST" action="{{ route('pontos.destroy', $concorrente) }}" class="inline ms-3"
                                                onsubmit="return confirm('Remover o concorrente {{ $concorrente->nome }}?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">{{ __('Remover') }}</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
