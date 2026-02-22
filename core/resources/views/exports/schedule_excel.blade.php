<table>
    @foreach($context as $contextId => $cont)
        @foreach($cont['courses'] as $courseId => $courseData)
            @foreach($courseData['shifts'] as $shiftId => $shiftContent)
                {{-- 1. Nome do Curso (Mesclando todas as colunas: Horário + Dias) --}}
                <tr>
                    <th colspan="{{ count($days) + 1 }}" style="background-color: #4b5563; color: #ffffff; font-weight: bold; text-align: center; border: 1px solid #000000;">
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
                        <th style="background-color: #f3f4f6; font-weight: bold; border: 1px solid #000000; text-align: center; width: 150px;">
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

                {{-- ESPAÇAMENTO --}}
                <tr style="border: none;"></tr>
                <tr style="border: none;"></tr>

            @endforeach
        @endforeach 
    @endforeach 
</table>