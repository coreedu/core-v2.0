<x-filament::page>
    <div class="space-y-3">
        {{-- Cabeçalho de contexto --}}
        <div class="flex flex-row items-center gap-6">
            <div>
                <strong>Versão:</strong> {{ $this->record->version }}
            </div>

            <div>
                <strong>Curso:</strong> {{ $this->record->course->nome }}
            </div>

            <div>
                <strong>Módulo:</strong> {{ $this->record->module_id }}º módulo
            </div>

            <div>
                <strong>Categoria:</strong> {{ $this->record->timeConfig->context->name }}
            </div>

            <div>
                <strong>Turno:</strong> {{ $this->record->timeConfig->shift->name }}
            </div>
        </div>
        <div class="flex justify-end">
            <x-filament::button wire:click="makeSchedule" color="primary" class="mr-2">
                Gerar Grade
            </x-filament::button>
            <x-filament::button wire:click="saveSchedule" color="primary">
                Salvar Grade
            </x-filament::button>
        </div>
        <div class="">
            {{-- Tabela de horários --}}
            <div class="w-[100%] overflow-x-auto shadow rounded-lg border border-gray-700">
                @include('filament.resources.schedule-resource.partials.schedule-table', [
                    'columns' => $this->days,
                    'timeSlots' => $this->timeSlots,
                    'scheduleData' => $this->scheduleData,
                    'subjects' => $this->subjects,
                    'rooms' => $this->rooms
                ])
            </div>
        </div>
    </div>
</x-filament::page>
