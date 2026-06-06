<div>
    <x-input-label for="nome" :value="__('Nome')" />
    <x-text-input id="nome" name="nome" type="text" class="mt-1 block w-full"
        :value="old('nome', $cliente->nome)" required autofocus />
    <x-input-error class="mt-2" :messages="$errors->get('nome')" />
</div>

<div class="mt-4">
    <x-input-label for="segmento" :value="__('Segmento')" />
    <x-text-input id="segmento" name="segmento" type="text" class="mt-1 block w-full"
        :value="old('segmento', $cliente->segmento)" placeholder="Ex.: Varejo, Alimentação..." />
    <x-input-error class="mt-2" :messages="$errors->get('segmento')" />
</div>

<div class="mt-4">
    <x-input-label for="observacoes" :value="__('Observações')" />
    <textarea id="observacoes" name="observacoes" rows="4"
        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('observacoes', $cliente->observacoes) }}</textarea>
    <x-input-error class="mt-2" :messages="$errors->get('observacoes')" />
</div>
