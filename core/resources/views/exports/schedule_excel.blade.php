<table>
    @foreach($context as $contextId => $cont)
        @foreach($cont['courses'] as $courseId => $courseData)
            @foreach($courseData['shifts'] as $shiftId => $shiftContent)
                {{-- 1. Nome do Curso (Mesclando todas as colunas: Horário + Dias) --}}
                <tr>
                    <th colspan="{{ (count($days) * 2) + 1 }}" style="background-color: #4b5563; color: #ffffff; font-weight: bold; text-align: center; border: 1px solid #000000;">
                        {{ $courseData['name'] . ' - ' . $shifts[$shiftId] . ' - ' . $cont['name'] }}
                    </th>
                </tr>

                {{-- 2. Cabeçalho dos Dias --}}
                <tr>
                    {{-- Coluna vazia em cima do Horário --}}
                    <th style="background-color: #f3f4f6; font-weight: bold; border: 1px solid #000000; text-align: center;">
                        Horário
                    </th>
                    
                    {{-- Loop dos Dias (Segunda a Sexta) --}}
                    @foreach($days as $dayId => $dayName)
                        <th colspan="2" style="background-color: #f3f4f6; font-weight: bold; border: 1px solid #000000; text-align: center;">
                            {{ $dayName }}
                        </th>
                    @endforeach
                </tr>
            
                {{-- Agora iteramos sobre os horários específicos DESTE turno --}}
                @foreach($times[$shiftId] as $timeId => $timeLabel)
                    @php
                        $moduleId = array_key_first($shiftContent['modules'] ?? []);
                        $daysData = $shiftContent['modules'][$moduleId]['days'] ?? [];
                    @endphp

                    {{-- LINHA 1: Horário e Componente --}}
                    <tr>
                        <td rowspan="3" style="vertical-align: center; border: 1px solid #000; text-align: center;">
                            {{ $timeLabel }}
                        </td>

                        @foreach($days as $dayId => $dayName)
                            @php
                                $groups = $daysData[$dayId]['times'][$timeId]['groups'] ?? [];
                                ksort($groups);
                            @endphp

                            @if(count($groups) == 2)
                                @foreach($groups as $group)
                                    <td style="border: 1px solid #000; font-weight: bold; text-align: center;">
                                        {{ $group['subject'] }}
                                    </td>
                                @endforeach
                            @else
                                <td colspan="2" style="border: 1px solid #000; font-weight: bold; text-align: center;">
                                    {{ $groups['A']['subject'] ?? '-' }}
                                </td>
                            @endif
                        @endforeach
                    </tr>

                    {{-- LINHA 2: Instrutor --}}
                    <tr>
                        @foreach($days as $dayId => $dayName)
                            @php
                                $groups = $daysData[$dayId]['times'][$timeId]['groups'] ?? [];
                                ksort($groups);
                            @endphp

                            @if(count($groups) == 2)
                                @foreach($groups as $group)
                                    <td style="border: 1px solid #000; text-align: center;">
                                        {{ $group['teacher'] }}
                                    </td>
                                @endforeach
                            @else
                                <td colspan="2" style="border: 1px solid #000;  font-weight: bold; text-align: center;">
                                    {{ $groups['A']['teacher'] ?? '-' }}
                                </td>
                            @endif
                        @endforeach
                    </tr>

                    {{-- LINHA 3: Sala --}}
                    <tr>
                        @foreach($days as $dayId => $dayName)
                            @php
                                $groups = $daysData[$dayId]['times'][$timeId]['groups'] ?? [];
                                ksort($groups);
                            @endphp

                            @if(count($groups) == 2)
                                @foreach($groups as $group)
                                    <td style="border: 1px solid #000; font-style: italic; text-align: center;">
                                        {{ $group['room'] }}
                                    </td>
                                @endforeach
                            @else
                                <td colspan="2" style="border: 1px solid #000; text-align: center;">
                                    {{ $groups['A']['room'] ?? '-' }}
                                </td>
                            @endif
                        @endforeach
                    </tr>
                @endforeach

                {{-- ESPAÇAMENTO --}}
                <tr style="border: none;"></tr>
                <tr style="border: none;"></tr>

            @endforeach
        @endforeach 
    @endforeach 
</table>