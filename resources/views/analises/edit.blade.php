<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar análise') }} — {{ $analise->cliente->nome }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('analises.update', $analise) }}">
                    @csrf
                    @method('PUT')
                    @include('analises.partials.form')

                    <div class="mt-6 flex items-center gap-4">
                        <x-primary-button>{{ __('Atualizar') }}</x-primary-button>
                        <a href="{{ route('analises.show', $analise) }}" class="text-sm text-gray-600 hover:text-gray-900">
                            {{ __('Cancelar') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
