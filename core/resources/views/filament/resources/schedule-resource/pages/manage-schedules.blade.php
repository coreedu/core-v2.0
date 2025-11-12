<x-filament::page>
    <div class="space-y-3">
        {{-- Cabeçalho de contexto --}}
        <div class="flex flex-row items-center gap-6">
            <div>
                <strong>Curso:</strong> {{ $this->record->course->nome }}
            </div>

            <div>
                <strong>Módulo:</strong> {{ $this->record->module_id }}º módulo
            </div>

            <div>
                <strong>Turno:</strong> {{ $this->record->shift->name }}
            </div>
        </div>
        <div class="flex justify-end">
            <x-filament::button wire:click="saveSchedule" color="primary">
                Salvar Grade
            </x-filament::button>
        </div>
        <div class="flex gap-6">
            {{-- Tabela de horários --}}
            <div class="md:w-[70%] w-full overflow-x-auto shadow rounded-lg border border-gray-700">
                @include('filament.resources.schedule-resource.partials.schedule-table', [
                    'columns' => $this->days,
                    'timeSlots' => $this->timeSlots,
                    'scheduleData' => $this->scheduleData,
                    'subjects' => $this->subjects,
                    'rooms' => $this->rooms
                ])
            </div>

            {{--Tabela de Sábado --}}
            <div class="md:w-[30%] w-full overflow-x-auto shadow rounded-lg border border-gray-700">
                @include('filament.resources.schedule-resource.partials.schedule-table', [
                    'columns' => ['6' => 'Sábado'],
                    'timeSlots' => $this->saturdayTimes,
                    'scheduleData' => $this->scheduleData,
                    'subjects' => $this->subjects,
                    'rooms' => $this->rooms
                ])
            </div>
        </div>
    </div>
</x-filament::page>
