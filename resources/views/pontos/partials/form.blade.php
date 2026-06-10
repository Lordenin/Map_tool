<input type="hidden" name="tipo" value="{{ old('tipo', $ponto->tipo) }}" />

<div>
    <x-input-label for="nome" :value="__('Nome')" />
    <x-text-input id="nome" name="nome" type="text" class="mt-1 block w-full"
        :value="old('nome', $ponto->nome)" required autofocus />
    <x-input-error class="mt-2" :messages="$errors->get('nome')" />
</div>

<div class="mt-4">
    <x-input-label for="endereco" :value="__('Endereço')" />
    <x-text-input id="endereco" name="endereco" type="text" class="mt-1 block w-full"
        :value="old('endereco', $ponto->endereco)" />
    <x-input-error class="mt-2" :messages="$errors->get('endereco')" />
</div>

<div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <x-input-label for="latitude" :value="__('Latitude')" />
        <x-text-input id="latitude" name="latitude" type="text" inputmode="decimal" class="mt-1 block w-full"
            :value="old('latitude', $ponto->latitude)" required placeholder="-23.5505" />
        <x-input-error class="mt-2" :messages="$errors->get('latitude')" />
    </div>
    <div>
        <x-input-label for="longitude" :value="__('Longitude')" />
        <x-text-input id="longitude" name="longitude" type="text" inputmode="decimal" class="mt-1 block w-full"
            :value="old('longitude', $ponto->longitude)" required placeholder="-46.6333" />
        <x-input-error class="mt-2" :messages="$errors->get('longitude')" />
    </div>
</div>
<p class="mt-2 text-xs text-gray-500">
    {{ __('Dica: no Google Maps, clique com o botão direito no local e clique nas coordenadas para copiá-las (formato "lat, lng").') }}
</p>

<div class="mt-4">
    <x-input-label for="observacoes" :value="__('Observações')" />
    <textarea id="observacoes" name="observacoes" rows="3"
        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('observacoes', $ponto->observacoes) }}</textarea>
    <x-input-error class="mt-2" :messages="$errors->get('observacoes')" />
</div>
