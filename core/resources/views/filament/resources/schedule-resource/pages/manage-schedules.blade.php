<x-filament::page>
    <div class="space-y-6">
        {{-- Cabeçalho de contexto --}}
        <div class="flex flex-col gap-1">
            <h2 class="text-xl font-bold">Gerenciando Grade Horária</h2>
            <div class="text-gray-600">
                <strong>Curso:</strong> {{ $record->course->nome }} <br>
                <strong>Módulo:</strong> {{ $record->module_id }}º módulo <br>
                <strong>Turno:</strong> {{ $record->shift->name }}
            </div>
        </div>

        {{-- Tabela de horários --}}
        <div class="overflow-x-auto bg-white shadow rounded-lg border">
            <table class="min-w-full text-sm text-center border-collapse">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-3 py-2 border">Horário</th>
                        @foreach(['Seg', 'Ter', 'Qua', 'Qui', 'Sex'] as $day)
                            <th class="px-3 py-2 border">{{ $day }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($timeSlots as $time)
                        <tr>
                            <td class="border px-2 py-2 font-medium">{{ $time->label }}</td>
                            @foreach(['monday','tuesday','wednesday','thursday','friday'] as $day)
                                <td class="border px-2 py-2">
                                    {{-- Matéria --}}
                                    <x-filament::input.wrapper>
                                        <x-filament::input.select wire:model.defer="scheduleData.{{ $day }}.{{ $time->id }}.subject_id">
                                            <option value="">Matéria</option>
                                            @foreach($subjects as $subject)
                                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                            @endforeach
                                        </x-filament::input.select>
                                    </x-filament::input.wrapper>

                                    {{-- Professor --}}
                                    <x-filament::input.wrapper>
                                        <x-filament::input.select wire:model.defer="scheduleData.{{ $day }}.{{ $time->id }}.teacher_id">
                                            <option value="">Professor</option>
                                            @foreach($teachers as $teacher)
                                                <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                            @endforeach
                                        </x-filament::input.select>
                                    </x-filament::input.wrapper>

                                    {{-- Sala --}}
                                    <x-filament::input.wrapper>
                                        <x-filament::input.select wire:model.defer="scheduleData.{{ $day }}.{{ $time->id }}.room_id">
                                            <option value="">Sala</option>
                                            @foreach($rooms as $room)
                                                <option value="{{ $room->id }}">{{ $room->name }}</option>
                                            @endforeach
                                        </x-filament::input.select>
                                    </x-filament::input.wrapper>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <x-filament::button wire:click="saveSchedule" color="primary">
            Salvar Grade
        </x-filament::button>
    </div>
</x-filament::page>
