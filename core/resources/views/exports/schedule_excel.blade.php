<table>
    @foreach($courses as $courseId => $courseData)
    {{-- ... (cabeçalhos do curso e dias) --}}

    @foreach($courseData['shifts'] as $shiftId => $shiftContent)
        {{-- Linha opcional para identificar o turno (Matutino, Vespertino, etc) --}}
        <tr>
            <td colspan="{{ count($days) + 1 }}" style="background-color: #e5e7eb; font-weight: bold;">
                Turno: {{ $shifts[$shiftId] ?? $shiftId }}
            </td>
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
                    {{ $timeLabel }} {{-- Agora $timeLabel é uma string (ex: "07:40 - 08:30") --}}
                </td>
                @foreach($days as $dayId => $dayName)
                    <td style="border: 1px solid #000; font-weight: bold;">
                        {{ $daysData[$dayId]['times'][$timeId]['groups']['A']['subject'] ?? '-' }}
                    </td>
                @endforeach
            </tr>

            {{-- LINHA 2: Instrutor --}}
            <tr>
                @foreach($days as $dayId => $dayName)
                    <td style="border: 1px solid #000;">
                        {{ $daysData[$dayId]['times'][$timeId]['groups']['A']['teacher'] ?? '-' }}
                    </td>
                @endforeach
            </tr>

            {{-- LINHA 3: Sala --}}
            <tr>
                @foreach($days as $dayId => $dayName)
                    <td style="border: 1px solid #000; font-style: italic;">
                        {{ $daysData[$dayId]['times'][$timeId]['groups']['A']['room'] ?? '-' }}
                    </td>
                @endforeach
            </tr>
        @endforeach
    @endforeach
@endforeach 
</table>