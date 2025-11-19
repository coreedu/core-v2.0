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
                vertical-align: middle;
                /* font-size: 9px; */
                color: #333333;
            }

            .textBig{
                font-size: 14px;
                /* color: #bd0e0eff; */
            }

            .textSmall{
                font-size: 10px;
                /* color: #bd0e0eff; */
            }

            .timeText{
                font-size: 12px;
                text-align: center;
                vertical-align: middle;
                color: #bd0e0eff;
            }

            .tabelaHorario th {
                background-color: #6c88d3;
                color: #ffffff;
                /* font-weight: 600; */
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
                font-size: 11px;
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

            .tabelaHorario td{
                width: 110px;              /* ajuste como quiser */
                height: 40px;              /* altura fixa */
                max-height: 70px;
                vertical-align: top;
                overflow: hidden;
            }

            /* linha que contém as duas tabelas */
            .pdf-row {
                width: 100%;
                display: block;
                text-align: left;
            }

            /* coluna esquerda: 70% */
            .pdf-col-left {
                display: inline-block;
                width: 69%;       /* precisa ser < 70% por causa do inline-block */
                vertical-align: top;
            }

            /* coluna direita: 30% */
            .pdf-col-right {
                display: inline-block;
                width: 29%;       /* precisa ser < 30% */
                vertical-align: top;
            }
        </style>
    </head>
    <body>
        <div id="header">
            <table width="100%">
                <tr>
                    <td width="30%">
                        <img src="{{ public_path('images/logo.svg') }}" style="height: 30px; margin-right: 5px;">
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
                    <div class="pdf-row">
                        <div class="pdf-col-left">
                            @include('filament.resources.schedule-resource.partials.pdf-table', [
                                'schedule' => $schedule,
                                'curso' => $sc,
                                'cursoName' => $sc['name'],
                                'times' => $schedule['times'][$idxShift],
                                'days' => $schedule['days'],
                                'module' => $module,
                                'idxModule' => $idxModule,
                                'shift' => $schedule['shifts'][$idxShift]
                            ])
                        </div>

                        <div class="pdf-col-right">
                            @include('filament.resources.schedule-resource.partials.pdf-table', [
                                'schedule' => $schedule,
                                'curso' => $sc,
                                'cursoName' => $sc['abreviacao'],
                                'times' => $schedule['satSlots'],
                                'days' => [7 => 'Sábado'],
                                'module' => $module,
                                'idxModule' => $idxModule,
                                'shift' => ''
                            ])
                        </div>
                    </div>

                    {{-- Só quebra página se NÃO for o último item --}}
                    @if(!($loop->parent->parent->last && $loop->parent->last && $loop->last))
                        <div class="page-break"></div>
                    @endif
                @endforeach
            @endforeach
        @endforeach
        <div id="footer">
            Core - Controle de Recursos Educacionais - Emitido em {{ now()->format('d/m/Y H:i') }}
        </div>
    </body>
</html>