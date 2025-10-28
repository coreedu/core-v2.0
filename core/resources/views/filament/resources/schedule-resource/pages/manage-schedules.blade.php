<x-filament::page>
    <div class="space-y-6">
        {{-- Cabeçalho de contexto --}}
        <div class="flex flex-col gap-1">
            <h2 class="text-xl font-bold">Gerenciando Grade Horária</h2>
            <div class="text-gray-600">
                <strong>Curso:</strong> {{ $this->record->course->nome }} <br>
                <strong>Módulo:</strong> {{ $this->record->module_id }}º módulo <br>
                <strong>Turno:</strong> {{ $this->record->shift->name }}
            </div>
        </div>

        {{-- Tabela de horários --}}
        <div class="overflow-x-auto shadow rounded-lg border">
            <table class="min-w-full text-sm text-center border-collapse">
                <thead>
                    <tr class="">
                        <th class="px-3 py-2 border">Horário</th>
                        @foreach($this->days as $day)
                            <th class="px-3 py-2 border">{{ $day }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($this->timeSlots as $idTime => $time)
                        <tr>
                            <td class="border px-2 py-2 font-medium">{{ $time }}</td>
                            @foreach($this->days as $day)
                                <td class="border px-2 py-2">
                                    {{-- Matéria --}}
                                    <x-filament::input.wrapper>
                                        <x-filament::input.select wire:model.defer="scheduleData.{{ $day }}.{{ $idTime }}.subject_id">
                                            <option value="">Matéria</option>
                                            @foreach($this->subjects as $idSubject => $subject)
                                                dd($subject);
                                                <option value="{{ $idSubject }}">{{ $subject['name'] }}</option>
                                            @endforeach
                                        </x-filament::input.select>
                                    </x-filament::input.wrapper>

                                    {{-- Professor --}}
                                    <x-filament::input.wrapper>
                                        <x-filament::input.select wire:model.defer="scheduleData.{{ $day }}.{{ $idTime }}.teacher_id">
                                            <option value="">Professor</option>
                                        </x-filament::input.select>
                                    </x-filament::input.wrapper>

                                    {{-- Sala --}}
                                    <x-filament::input.wrapper>
                                        <x-filament::input.select wire:model.defer="scheduleData.{{ $day }}.{{ $idTime }}.room_id">
                                            <option value="">Sala</option>
                                            @foreach($this->rooms as $idRoom => $room)
                                                <option value="{{ $idRoom }}">{{ $room['name'] ?? $room['type'] . ' '.  $room['number'] }}</option>
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
