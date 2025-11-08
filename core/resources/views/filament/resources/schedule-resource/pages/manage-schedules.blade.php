<x-filament::page>
    <div class="space-y-6">
        {{-- Cabeçalho de contexto --}}
        <div class="flex flex-row items-center gap-6 text-gray-600">
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
                            @foreach($this->days as $idDay => $day)

                                @php
                                    $groups = $scheduleData[$idDay][$idTime]['groups'] ?? ['A' => null];
                                @endphp
                                <td class="border px-2 py-2 d-flex justify-between">

                                    @foreach($groups as $groupLetter => $groupData)
                                        <div class="">

                                        {{-- Matéria --}}
                                        <x-filament::input.wrapper>
                                            <x-filament::input.select
                                                wire:model="scheduleData.{{ $idDay }}.{{ $idTime }}.groups.{{ $groupLetter }}.subject_id"
                                                wire:change="$dispatch('subjectChanged', { day: '{{ $idDay }}', timeId: '{{ $idTime }}', group: '{{ $groupLetter }}', subjectId: $event.target.value }).to($wire)"
                                            >
                                                <option value="">Matéria</option>
                                                @foreach ($this->subjects as $idSubject => $subject)
                                                    <option value="{{ $idSubject }}"selected="{{ isset($this->scheduleData[$idDay][$idTime]['groups'][$groupLetter]['subject_id']) }}">{{ $subject['name'] }}</option>
                                                @endforeach
                                            </x-filament::input.select>
                                        </x-filament::input.wrapper>

                                        {{-- Professor --}}
                                        <x-filament::input.wrapper>
                                            <x-filament::input.select wire:model="scheduleData.{{ $idDay }}.{{ $idTime }}.groups.{{ $groupLetter }}.teacher_id">
                                                @if(!empty($this->scheduleData[$idDay][$idTime]['groups'][$groupLetter]['available_teachers']))
                                                    @foreach($this->scheduleData[$idDay][$idTime]['groups'][$groupLetter]['available_teachers'] as $idTeacher => $teacher)
                                                        <option value="{{ $idTeacher }}">{{ $teacher }}</option>
                                                    @endforeach
                                                @endif
                                                <option value="">Professor</option>
                                            </x-filament::input.select>
                                        </x-filament::input.wrapper>

                                        {{-- Sala --}}
                                        <x-filament::input.wrapper>
                                            <x-filament::input.select wire:model="scheduleData.{{ $idDay }}.{{ $idTime }}.groups.{{ $groupLetter }}.room_id">
                                                <option value="">Sala</option>
                                                @foreach($this->rooms as $idRoom => $room)
                                                    <option value="{{ $idRoom }}">{{ $room['name'] ?? $room['type'] . ' '.  $room['number'] }}</option>
                                                @endforeach
                                            </x-filament::input.select>
                                        </x-filament::input.wrapper>
                                        
                                        </div>
                                    @endforeach

                                    {{-- Botões --}}
                                    <div class="flex justify-center gap-1 mt-2">
                                        @if(count($groups) === 1)
                                            <x-filament::button color="gray" size="xs"
                                                wire:click="splitGroup('{{ $idDay }}', '{{ $idTime }}')">
                                                Dividir Turma
                                            </x-filament::button>
                                        @else
                                            <x-filament::button color="danger" size="xs"
                                                wire:click="mergeGroups('{{ $idDay }}', '{{ $idTime }}')">
                                                Unir Turmas
                                            </x-filament::button>
                                        @endif
                                    </div> 
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-filament::page>
