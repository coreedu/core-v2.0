<html>
    <head>
        <style>
            @page {
                margin: 40px 25px;
            }

            /* Rodapé fixo */
            #footer {
                position: fixed;
                bottom: -15px;
                left: 0;
                right: 0;
                height: 30px;

                text-align: center;
                font-size: 11px;
                color: #555;
            }

            /* Estilização geral da tabela */
            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
                font-size: 12px;
            }

            th, td {
                border: 1px solid #000;
                padding: 6px;
            }

            th {
                background: #f2f2f2;
                font-weight: bold;
            }

            .page-break {
                page-break-after: always;
            }
        </style>
    </head>
    <body>
        @foreach($schedule['courses'] as $sc)
            @foreach($sc['shifts'] as $idxShift => $shift)
                @foreach($shift['modules'] as $idxModule => $module)
                    <div class="d-inline-flex me-4">
                        <table class="text-sm text-center border-collapse bg-white">
                            <thead>
                                <tr>
                                    <th class="px-3 py-2 border" colspan="{{count($schedule['days'])+1}}">{{$idxModule}}° {{$sc['name'] ?? 'Nome Curso'}} - {{$schedule['shifts'][$idxShift]}}</th>
                                </tr>
                                <tr>
                                    <th class="border">
                                        
                                    </th>
                                    @foreach($schedule['days'] as $idxDay => $day)
                                        <th class="px-3 py-2 border">{{ $day }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($schedule['times'][$idxShift] as $idxTime => $time)
                                    <tr>
                                        <td class="px-3 py-2 border">
                                            {{$time}}
                                        </td>
                                        @foreach($schedule['days'] as $idxDay => $day)
                                            @if(isset($module['days'][$idxDay]['times'][$idxTime]))
                                                <td class="px-3 py-2 border">
                                                    <div class="d-flex justify-content-center gap-4">
                                                        @foreach($module['days'][$idxDay]['times'][$idxTime]['groups'] as $group)
                                                            <div class="d-flex flex-column"> 
                                                                <span>{{$group['subject']}}</span>
                                                                <span>{{$group['teacher']}}</span>
                                                                <span>{{$group['room']}}</span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </td>
                                            @else
                                                <td class="px-3 py-2 border">-</td>
                                            @endif
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        
                        </table>
                    </div>
                    <div class="page-break"></div>
                @endforeach
            @endforeach
        @endforeach
        <div id="footer">
            Gerado em {{ now()->format('d/m/Y H:i') }}
        </div>
    </body>
</html>