<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Relatório de Ambientes</title>
    <style>
        :root {
            --white-color: #ffffff;
            --primary-color: #233c6f;
            --secondary-color: #4a64a3;
            --section-bg-color: #e4e8f3;
            --dark-color: #0f182f;
            --p-color: #5e6475;
            --success-color: #4CAF50;
            --warning-color: #F4B400;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: var(--dark-color);
            margin: 10px;
            background-color: var(--white-color);
        }

        .header-table {
            width: 100%;
            border-bottom: 2px solid var(--secondary-color);
            margin-bottom: 8px;
            border-collapse: collapse;
        }

        .header-table td {
            vertical-align: middle;
            border: none;
        }

        .title-cell {
            text-align: left;
        }

        .logo-cell {
            text-align: right;
        }

        .logo-cell img {
            width: 65px;
        }

        h1 {
            color: var(--primary-color);
            font-size: 20px;
            margin: 0;
        }

        .meta {
            font-size: 11px;
            color: var(--p-color);
            text-align: center;
            margin: 15px 0 25px 0;
        }

        .room-header {
            background: var(--section-bg-color);
            border-left: 4px solid var(--secondary-color);
            padding: 8px 10px;
            margin-top: 15px;
            margin-bottom: 8px;
            border-radius: 4px;
            font-weight: bold;
        }

        .room-header span {
            margin-right: 12px;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            table-layout: fixed;
        }

        th,
        td {
            padding: 6px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
            text-align: center;
            word-wrap: break-word;
        }

        thead {
            background-color: var(--secondary-color);
            color: var(--white-color);
            font-weight: bold;
            font-size: 10px;
        }

        td {
            font-size: 9px;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        th:nth-child(1),
        td:nth-child(1) {
            width: 22%;
        }

        th:nth-child(2),
        td:nth-child(2) {
            width: 15%;
        }

        th:nth-child(3),
        td:nth-child(3) {
            width: 15%;
        }

        th:nth-child(4),
        td:nth-child(4) {
            width: 15%;
        }

        th:nth-child(5),
        td:nth-child(5) {
            width: 13%;
        }

        th:nth-child(6),
        td:nth-child(6) {
            width: 20%;
        }

        .status {
            font-weight: bold;
        }

        .status.disponivel {
            color: var(--success-color);
        }

        .status.indisponivel {
            color: var(--warning-color);
        }

        .no-equip {
            font-style: italic;
            color: var(--p-color);
            padding-left: 8px;
        }

        footer {
            text-align: center;
            font-size: 10px;
            color: var(--p-color);
            border-top: 1px solid #ddd;
            padding-top: 6px;
            margin-top: 40px;
        }
    </style>
</head>

<body>
    <table class="header-table">
        <tr>
            <td class="title-cell">
                <h1>Relatório de Ambientes</h1>
            </td>
            <td class="logo-cell">
                <img src="{{ public_path('images/logo.svg') }}" alt="Logo">
            </td>
        </tr>
    </table>

    <div class="meta">
        Relatório emitido em {{ now()->format('d/m/Y \à\s H:i') }}
        por <strong>{{ auth()->user()->name ?? 'Usuário do Sistema' }}</strong><br>
        Sistema <strong>Core — Controle de Recursos Educacionais</strong>
    </div>

    @foreach ($rooms as $room)
        <div class="room-header">
            <span>{{'Sala ' . $room->number }}</span>
            <span>| Status: <strong>{{ $room->active ? 'Ativa' : 'Inativa' }}</strong></span>
        </div>

            @if ($room->totalEquipments())
                <table>
                    <thead>
                        <tr>
                            <td>Nome</td>
                            <td>Marca</td>
                            <td>Tipo</td>
                            <td>Patrimônio</td>
                            <td>Status</td>
                        </tr>
                    </thead>
                </table>
                @foreach ($room->getGroups() as $group)

                    <h5>Grupo: {{ $group->name }}</h5>

                    @php
                        $equipments = $group->getEquipments();
                    @endphp

                    @if ($equipments->count())
                        <table>
                            @foreach ($equipments as $eq)
                                <tr>
                                    <td>{{ $eq->name }}</td>
                                    <td>{{ $eq->brand->name ?? '-' }}</td>
                                    <td>{{ $eq->type->name ?? '-' }}</td>
                                    <td>{{ $eq->patrimony ?? '-' }}</td>
                                    <<td class="status {{ $eq->status ? 'disponivel' : 'indisponivel' }}">
                                        {{ $eq->status ? 'Disponível' : 'Indisponível' }}
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    @else
                        <p>Nenhum equipamento neste grupo.</p>
                    @endif

                @endforeach
            @else
                <p class="no-equip">Nenhum equipamento vinculado.</p>
            @endif
    @endforeach
    <footer>
        © {{ now()->year }} Sistema Core — Controle de Recursos Educacionais. Todos os direitos reservados.
    </footer>
</body>

</html>