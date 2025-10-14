
<x-filament::page>
    <div class="flex justify-end">
        <x-filament::button wire:click="submit" color="success" class="mt-4" size="sm">
            Salvar Disponibilidade
        </x-filament::button>
    </div>
    {{ $this->form }}
</x-filament::page>
