<table class="max-w-full w-full text-sm text-center border-collapse">
    <thead>
        <tr class="">
            <th class="px-3 py-2 border">Horário</th>
            @foreach($columns as $day)
                <th class="px-3 py-2 border">{{ $day }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($timeSlots as $idTime => $time)
            <tr>
                @php
                    [$start, $end] = explode(' - ', $time);
                @endphp

                <td class="border px-2 py-2 text-center font-medium leading-tight">
                    <span class="block">{{ $start }}</span>
                    <span class="block text-sm text-gray-400">-</span>
                    <span class="block">{{ $end }}</span>
                </td>

                @foreach($columns as $idDay => $day)
                    @php
                        $isSlotEnabled = in_array("{$idDay}-{$idTime}", $this->enabledSlots);
                        $groups = $scheduleData[$idDay][$idTime]['groups'] ?? ['A' => null];
                    @endphp
                    <td class="border">
                        <div class="flex flex-col md:flex-row gap-2 justify-center items-start">
                            @foreach($groups as $groupLetter => $groupData)
                                <div class="w-full">
                                    {{-- Matéria --}}
                                    <x-filament::input.wrapper class="w-full">
                                        <x-filament::input.select
                                            wire:model="scheduleData.{{ $idDay }}.{{ $idTime }}.groups.{{ $groupLetter }}.subject_id"
                                            wire:change="$dispatch('subjectChanged', { day: '{{ $idDay }}', timeId: '{{ $idTime }}', group: '{{ $groupLetter }}', subjectId: $event.target.value }).to($wire)"
                                            :disabled="!$isSlotEnabled"
                                            class="w-full"
                                        >
                                            <option value="">Matéria</option>
                                            @foreach ($subjects as $idSubject => $subject)
                                                <option value="{{ $idSubject }}"selected="{{ isset($scheduleData[$idDay][$idTime]['groups'][$groupLetter]['subject_id']) }}">{{ $subject['name'] }}</option>
                                            @endforeach
                                        </x-filament::input.select>
                                    </x-filament::input.wrapper>
                                    
                                    {{-- Professor --}}
                                    <x-filament::input.wrapper class="w-full">
                                        <x-filament::input.select 
                                            wire:key="select-teacher-{{ $scheduleVersion }}-{{ $idDay }}-{{ $idTime }}"
                                            wire:model="scheduleData.{{ $idDay }}.{{ $idTime }}.groups.{{ $groupLetter }}.teacher_id" 
                                            class="w-full" 
                                            :disabled="!$isSlotEnabled"
                                        >
                                            @if(!empty($scheduleData[$idDay][$idTime]['groups'][$groupLetter]['available_teachers']))
                                                @foreach($scheduleData[$idDay][$idTime]['groups'][$groupLetter]['available_teachers'] as $idTeacher => $teacher)
                                                    <option value="{{ $idTeacher }}">{{ $teacher }}</option>
                                                @endforeach
                                            @endif
                                            <option value="">Professor</option>
                                        </x-filament::input.select>
                                    </x-filament::input.wrapper>

                                    {{-- Sala --}}
                                    <x-filament::input.wrapper class="w-full">
                                        <x-filament::input.select 
                                            wire:model="scheduleData.{{ $idDay }}.{{ $idTime }}.groups.{{ $groupLetter }}.room_id"
                                            class="w-full"
                                            :disabled="!$isSlotEnabled"
                                        >
                                            <option value="">Sala</option>
                                            @foreach($rooms as $idRoom => $room)
                                                <option value="{{ $idRoom }}">{{ $room['name'] ?? $room['type'] . ' '.  $room['number'] }}</option>
                                            @endforeach
                                        </x-filament::input.select>
                                    </x-filament::input.wrapper>
                                </div>
                            @endforeach
                        </div>
                        {{-- Botões --}}
                        <div class="flex justify-center gap-1">
                            @if(count($groups) === 1)
                                <x-filament::button color="primary" size="xs"
                                    class="w-full md:w-auto"
                                    wire:click="splitGroup('{{ $idDay }}', '{{ $idTime }}')">
                                    Dividir Turma
                                </x-filament::button>
                            @else
                                <x-filament::button color="gray" size="xs"
                                    class="w-full md:w-auto"
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
