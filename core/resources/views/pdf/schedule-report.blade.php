<html>
    <head>
        <style>
            @page {
                margin: 40px 25px;
            }

            body {
                font-family: "Lato", sans-serif;
                color: #333;
            }

            /* Rodapé fixo */
            #footer {
                position: fixed;
                bottom: -15px;
                left: 0;
                right: 0;
                height: 30px;

                text-align: left;
                font-size: 11px;
                color: #555;
            }

            .curso {
                margin: 15px auto;
                max-width: 95%;
                background-color: #ffffff;
                border-radius: 8px;
                padding: 5px;
            }

            .tabelaHorario {
                width: 100%;
                border-collapse: collapse;
                background-color: #ffffff;
                page-break-inside: avoid;
                border-radius: 8px;
                overflow: hidden; 
            }

            .tabelaHorario th,
            .tabelaHorario td {
                border: 1px solid #dde1e7;
                padding: 5px;
                text-align: center;
                font-size: 11px;
                color: #333333;
            }

            .tabelaHorario th {
                background-color: #6c88d3;
                color: #ffffff;
                font-weight: 600;
            }

            .thead tr td {
                font-weight: bold;
                font-size: 11px;
                background-color: #8497c0;
                color: white;
                padding: 6px;
            }

            .thead th {
                color: white;
                padding: 6px;
            }

            td i {
                font-size: 14px;
                color: #6c88d3;
            }

            tr:nth-child(even) {
                background-color: #eef3fd;
            }

            tr:nth-child(odd) {
                background-color: #ffffff;
            }

            .tabelaHorario td:hover {
                background-color: #f2f4ff;
                transition: background-color 0.2s ease;
            }

            .page-break {
                page-break-after: always;
            }

            #header {
                position: fixed;
                top: -20px;
                left: 0;
                right: 0;
                text-align: center;
            }

            #header img {
                height: 60px; 
            }

            .group-container {
                display: block;
                text-align: center; 
            }

            .group-block {
                display: inline-block;
                vertical-align: top;
                margin: 0 6px;        
                text-align: left;
            }

            .group-block span {
                display: block;       
                font-size: 11px;
                margin-bottom: 2px;
            }
        </style>
    </head>
    <body>
        <div id="header">
            <table width="100%">
            <tr>
                <td width="30%">
                    <img src="{{ public_path('images/logo.svg') }}" style="height: 30px; margin-right: 5px;">
                    <!-- <img src="resources\img\logo-fatec-cores.png" style="height: 30px;"> -->
                </td>
                <td width="40%"></td>
                <td width="30%" style="text-align: right; font-size: 12px;">
                    Core - Horário Acadêmico
                </td>
            </tr>
        </table>
        </div>
        @foreach($schedule['courses'] as $sc)
            @foreach($sc['shifts'] as $idxShift => $shift)
                @foreach($shift['modules'] as $idxModule => $module)
                    <div class="me-4 curso">
                        <table class="text-sm text-center border-collapse bg-white tabelaHorario">
                            <thead class="thead">
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
                                                    <div class="group-container">
                                                        @foreach($module['days'][$idxDay]['times'][$idxTime]['groups'] as $group)
                                                            <div class="group block"> 
                                                                <span>{{Str::limit($group['subject'], 30, '...')}}</span>
                                                                <span>{{Str::limit($group['teacher'], 30, '...')}}</span>
                                                                <span>{{Str::limit($group['room'], 30, '...')}}</span>
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
            Core - Controle de Recursos Educacionais - Emitido em {{ now()->format('d/m/Y H:i') }}
        </div>
    </body>
</html>