<div>
    <x-input-label for="titulo" :value="__('Título')" />
    <x-text-input id="titulo" name="titulo" type="text" class="mt-1 block w-full"
        :value="old('titulo', $analise->titulo)" required autofocus />
    <x-input-error class="mt-2" :messages="$errors->get('titulo')" />
</div>

<div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <x-input-label for="status" :value="__('Status')" />
        <select id="status" name="status"
            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
            @foreach (\App\Models\Analise::STATUSES as $valor => $rotulo)
                <option value="{{ $valor }}"
                    @selected(old('status', $analise->status ?? \App\Models\Analise::STATUS_RASCUNHO) === $valor)>
                    {{ $rotulo }}
                </option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('status')" />
    </div>

    <div>
        <x-input-label for="data_analise" :value="__('Data da análise')" />
        <x-text-input id="data_analise" name="data_analise" type="date" class="mt-1 block w-full"
            :value="old('data_analise', $analise->data_analise?->format('Y-m-d'))" />
        <x-input-error class="mt-2" :messages="$errors->get('data_analise')" />
    </div>
</div>

<div class="mt-4">
    <x-input-label for="descricao" :value="__('Descrição')" />
    <textarea id="descricao" name="descricao" rows="4"
        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('descricao', $analise->descricao) }}</textarea>
    <x-input-error class="mt-2" :messages="$errors->get('descricao')" />
</div>
