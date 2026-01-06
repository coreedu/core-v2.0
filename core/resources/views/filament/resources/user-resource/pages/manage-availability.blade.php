
<x-filament::page>
    <div class="flex justify-between">
        <span class="text-lg">
            Nome:
            <strong class="text-gray-900">
                {{ $this->record->name }}
            </strong>
        </span>
        <x-filament::button wire:click="submit" color="success" class="mt-4" size="sm" icon="heroicon-o-bookmark">
            Salvar Disponibilidade
        </x-filament::button>
    </div>
    {{ $this->form }}
</x-filament::page>
