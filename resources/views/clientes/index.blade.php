<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Clientes') }}
            </h2>
            <a href="{{ route('clientes.create') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition">
                {{ __('Novo cliente') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 rounded-md bg-green-50 p-4 text-sm text-green-700 border border-green-200">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                @if ($clientes->isEmpty())
                    <div class="p-6 text-center text-gray-500">
                        {{ __('Nenhum cliente cadastrado ainda.') }}
                        <a href="{{ route('clientes.create') }}" class="text-indigo-600 hover:underline">
                            {{ __('Cadastrar o primeiro') }}
                        </a>.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">{{ __('Nome') }}</th>
                                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">{{ __('Segmento') }}</th>
                                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">{{ __('Análises') }}</th>
                                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">{{ __('Cadastrado por') }}</th>
                                    <th class="px-6 py-3 text-right font-medium text-gray-500 uppercase tracking-wider">{{ __('Ações') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($clientes as $cliente)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 font-medium text-gray-900">
                                            <a href="{{ route('clientes.show', $cliente) }}" class="hover:text-indigo-600">
                                                {{ $cliente->nome }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 text-gray-500">{{ $cliente->segmento ?: '—' }}</td>
                                        <td class="px-6 py-4 text-gray-500">{{ $cliente->analises_count }}</td>
                                        <td class="px-6 py-4 text-gray-500">{{ $cliente->user?->name ?? '—' }}</td>
                                        <td class="px-6 py-4 text-right whitespace-nowrap">
                                            <a href="{{ route('clientes.edit', $cliente) }}" class="text-indigo-600 hover:text-indigo-900">{{ __('Editar') }}</a>
                                            <form method="POST" action="{{ route('clientes.destroy', $cliente) }}" class="inline ms-3"
                                                onsubmit="return confirm('Remover o cliente {{ $cliente->nome }}? Esta ação não pode ser desfeita.');">
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

                    @if ($clientes->hasPages())
                        <div class="px-6 py-4 border-t border-gray-100">
                            {{ $clientes->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
