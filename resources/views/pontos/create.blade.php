@php $ehEstabelecimento = $ponto->tipo === \App\Models\Ponto::TIPO_ESTABELECIMENTO; @endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $ehEstabelecimento ? __('Definir estabelecimento') : __('Adicionar concorrente') }}
            <span class="text-gray-400">—</span> {{ $analise->titulo }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('analises.pontos.store', $analise) }}">
                    @csrf
                    @include('pontos.partials.form')

                    <div class="mt-6 flex items-center gap-4">
                        <x-primary-button>{{ __('Salvar') }}</x-primary-button>
                        <a href="{{ route('analises.show', $analise) }}" class="text-sm text-gray-600 hover:text-gray-900">
                            {{ __('Cancelar') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
